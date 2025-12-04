<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use App\Models\User;
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

        $csv = $orders->map(function (Order $order) {
            return [
                'Pedido' => $order->id,
                'Cliente' => $order->customer_name,
                'Correo' => $order->customer_email,
                'Estado' => OrderStatus::label($order->status),
                'Total' => $order->total,
                'Fecha' => $order->created_at?->format('Y-m-d H:i'),
                'Productos' => $order->items->sum('quantity'),
            ];
        });

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte-pedidos.csv"',
        ];

        $callback = function () use ($csv) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Pedido', 'Cliente', 'Correo', 'Estado', 'Total', 'Fecha', 'Productos']);
            foreach ($csv as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
