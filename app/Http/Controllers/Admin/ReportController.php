<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Support\SimplePdf;
use App\Support\OrderStatus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $month = (string) $request->query('month', now()->format('Y-m'));
        [$year, $monthNumber] = array_pad(explode('-', $month), 2, null);

        $ordersQuery = Order::query();
        if ($year && $monthNumber) {
            $ordersQuery->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
        }

        $orders = (clone $ordersQuery)->latest()->paginate(15)->withQueryString();
        $totalSales = (clone $ordersQuery)->sum('total');
        $ordersCount = (clone $ordersQuery)->count();

        $frequentCustomers = User::where('is_admin', false)
            ->withCount(['orders as orders_count' => function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            }])
            ->withSum(['orders as total_spent' => function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            }], 'total')
            ->orderByDesc('orders_count')
            ->limit(8)
            ->get();

        $topProducts = OrderItem::select('product_id', 'name')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(line_total) as total_amount')
            ->whereHas('order', function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            })
            ->groupBy('product_id', 'name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('admin.reports.monthly', [
            'month' => $month,
            'orders' => $orders,
            'totalSales' => $totalSales,
            'ordersCount' => $ordersCount,
            'frequentCustomers' => $frequentCustomers,
            'topProducts' => $topProducts,
            'contactNumber' => OrderStatus::CONTACT_NUMBER,
        ]);
    }

    public function monthlyPdf(Request $request)
    {
        $month = (string) $request->query('month', now()->format('Y-m'));
        [$year, $monthNumber] = array_pad(explode('-', $month), 2, null);

        $ordersQuery = Order::query();
        if ($year && $monthNumber) {
            $ordersQuery->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
        }

        $orders = (clone $ordersQuery)->with('items')->latest()->get();
        $totalSales = (clone $ordersQuery)->sum('total');
        $ordersCount = (clone $ordersQuery)->count();
        $frequentCustomers = User::where('is_admin', false)
            ->withCount(['orders as orders_count' => function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            }])
            ->withSum(['orders as total_spent' => function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            }], 'total')
            ->orderByDesc('orders_count')
            ->limit(8)
            ->get();

        $topProducts = OrderItem::select('product_id', 'name')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(line_total) as total_amount')
            ->whereHas('order', function ($query) use ($year, $monthNumber) {
                if ($year && $monthNumber) {
                    $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNumber);
                }
            })
            ->groupBy('product_id', 'name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $pdf = new SimplePdf();
        $pdf->addTitle('Reporte mensual');
        $pdf->addSubtitle('Periodo: ' . $month);
        $pdf->addKeyValueRows([
            'Ventas del mes' => 'S/ ' . number_format($totalSales, 2),
            'Pedidos emitidos' => $ordersCount,
            'Fecha de generación' => now()->format('d/m/Y H:i'),
            'Contacto de soporte' => OrderStatus::CONTACT_NUMBER,
        ]);

        $pdf->addParagraph('Productos más vendidos', 11);
        $pdf->addTable(
            ['Producto', 'Unidades', 'Total'],
            $topProducts->map(fn ($row) => [
                $row->name ?? 'Producto eliminado',
                $row->total_quantity,
                'S/ ' . number_format($row->total_amount, 2),
            ])->all(),
            [200, 90, 90]
        );

        $pdf->addParagraph('Clientes frecuentes', 11);
        $pdf->addTable(
            ['Cliente', 'Correo', 'Pedidos', 'Total'],
            $frequentCustomers->map(fn (User $customer) => [
                $customer->name,
                $customer->email,
                $customer->orders_count,
                'S/ ' . number_format($customer->total_spent ?? 0, 2),
            ])->all(),
            [180, 170, 80, 80]
        );

        $pdf->addParagraph('Pedidos del mes', 11);
        $pdf->addTable(
            ['Pedido', 'Cliente', 'Estado', 'Total', 'Fecha'],
            $orders->map(fn (Order $order) => [
                '#' . $order->id,
                $order->customer_name,
                OrderStatus::label($order->status),
                'S/ ' . number_format($order->total, 2),
                optional($order->created_at)->format('d/m/Y H:i'),
            ])->all(),
            [70, 170, 90, 80, 110]
        );

        return $pdf->download('reporte-mensual-' . $month . '.pdf');
    }
}
