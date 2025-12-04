<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Models\User;
use App\Support\SimplePdf;
use App\Support\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $month  = $request->query('month');

        $ordersQuery = Order::with('user')->latest();

        if ($status) {
            $ordersQuery->where('status', $status);
        }

        if ($month) {
            [$year, $monthNumber] = array_pad(explode('-', $month), 2, null);
            if ($year && $monthNumber) {
                $ordersQuery->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
            }
        }

        $orders = $ordersQuery->paginate(15)->withQueryString();

        $driver = Order::query()->getModel()->getConnection()->getDriverName();
        $periodExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlySales = Order::selectRaw($periodExpression . ' as period')
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(*) as orders_count')
            ->groupBy('period')
            ->orderByDesc('period')
            ->limit(6)
            ->get();

        $topCustomers = User::where('is_admin', false)
            ->withSum('orders as total_spent', 'total')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        return view('admin.orders.index', [
            'orders' => $orders,
            'monthlySales' => $monthlySales,
            'status' => $status,
            'month' => $month,
            'topCustomers' => $topCustomers,
            'statusOptions' => OrderStatus::options(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user']);

        return view('admin.orders.show', [
            'order' => $order,
            'statusOptions' => OrderStatus::options(),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(OrderStatus::options()))],
            'notify' => ['nullable', 'boolean'],
        ]);

        $order->update(['status' => $data['status']]);

        if ($request->boolean('notify')) {
            Mail::to($order->customer_email)->send(new OrderStatusUpdatedMail($order, $data['status']));
        }

        return back()->with('ok', 'Estado de pedido actualizado correctamente.');
    }

    public function report(Request $request)
    {
        $month = $request->query('month');
        $query = Order::query();

        if ($month) {
            [$year, $monthNumber] = array_pad(explode('-', $month), 2, null);
            if ($year && $monthNumber) {
                $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
            }
        }

        $orders = $query->with('items')->orderByDesc('created_at')->get();

        $totalSales = $orders->sum('total');
        $totalProducts = $orders->flatMap->items->sum('quantity');
        $statusSummary = $orders->groupBy('status')->map(fn ($group, $status) => OrderStatus::label($status) . ': ' . $group->count())->values()->implode(' | ');

        $pdf = new SimplePdf();
        $pdf->addTitle('Reporte de pedidos');
        $pdf->addSubtitle($month ? 'Periodo: ' . $month : 'Todos los periodos');
        $pdf->addKeyValueRows([
            'Pedidos procesados' => $orders->count(),
            'Total vendido' => 'S/ ' . number_format($totalSales, 2),
            'Productos vendidos' => $totalProducts,
            'Estados' => $statusSummary ?: 'Sin movimientos',
            'Fecha de generación' => now()->format('d/m/Y H:i'),
        ]);

        $rows = $orders->map(function (Order $order) {
            return [
                '#' . $order->id,
                $order->customer_name,
                OrderStatus::label($order->status),
                'S/ ' . number_format($order->total, 2),
                optional($order->created_at)->format('d/m/Y H:i'),
                $order->items->sum('quantity'),
            ];
        })->all();

        $pdf->addTable([
            'Pedido', 'Cliente', 'Estado', 'Total', 'Fecha', 'Productos'
        ], $rows, [70, 160, 90, 80, 120, 60]);

        return $pdf->download('reporte-pedidos.pdf');
    }

    public function downloadPdf(Order $order)
    {
        $order->load(['items', 'user']);

        $pdf = new SimplePdf();
        $pdf->addTitle('Detalle de pedido #' . $order->id);
        $pdf->addSubtitle('Generado el ' . now()->format('d/m/Y H:i'));

        $pdf->addKeyValueRows([
            'Cliente' => $order->customer_name . ' (' . $order->customer_email . ')',
            'Total' => 'S/ ' . number_format($order->total, 2),
            'Estado' => OrderStatus::label($order->status),
            'Creado' => optional($order->created_at)->format('d/m/Y H:i'),
        ]);

        $pdf->addParagraph('Datos de entrega:', 11);
        $pdf->addKeyValueRows([
            'Dirección' => $order->shipping_address ?? 'No registrada',
            'Ciudad' => $order->shipping_city ?? 'No indicada',
            'Referencia' => $order->shipping_reference ?? 'Sin referencia',
            'Pago' => $order->payment_method,
        ], 10);

        $pdf->addParagraph('Productos incluidos', 11);
        $pdf->addTable([
            'Producto', 'Cantidad', 'Total línea'
        ], $order->items->map(fn ($item) => [
            $item->name,
            $item->quantity,
            'S/ ' . number_format($item->line_total, 2),
        ])->all(), [240, 80, 100]);

        $pdf->addKeyValueRows([
            'Subtotal' => 'S/ ' . number_format($order->items->sum('line_total'), 2),
            'Total del pedido' => 'S/ ' . number_format($order->total, 2),
        ], 11);

        return $pdf->download('pedido-' . $order->id . '.pdf');
    }
}
