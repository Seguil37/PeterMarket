<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
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
}
