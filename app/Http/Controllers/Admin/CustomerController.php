<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $customersQuery = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->orderByDesc('orders_count');

        if ($search !== '') {
            $customersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $customersQuery->paginate(12)->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function exportPdf()
    {
        $customers = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->orderByDesc('orders_count')
            ->get();

        $pdf = new SimplePdf();
        $pdf->addTitle('Listado de clientes');
        $pdf->addSubtitle('Generado el ' . now()->format('d/m/Y H:i'));
        $pdf->addKeyValueRows([
            'Total de clientes' => $customers->count(),
            'Clientes con compras' => $customers->where('orders_count', '>', 0)->count(),
        ]);

        $rows = $customers->map(function (User $customer) {
            return [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->orders_count,
                'S/ ' . number_format($customer->total_spent ?? 0, 2),
            ];
        })->all();

        $pdf->addTable([
            'ID', 'Cliente', 'Correo', 'Pedidos', 'Total'
        ], $rows, [40, 160, 180, 70, 70]);

        return $pdf->download('clientes.pdf');
    }

    public function show(User $customer)
    {
        abort_if($customer->is_admin, 404);

        $orders = $customer->orders()->with('items')->latest()->paginate(10);

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function ordersPdf(User $customer)
    {
        abort_if($customer->is_admin, 404);

        $orders = $customer->orders()->with('items')->latest()->get();

        $pdf = new SimplePdf();
        $pdf->addTitle('Pedidos de ' . $customer->name);
        $pdf->addSubtitle('Correo: ' . $customer->email);
        $pdf->addKeyValueRows([
            'Total de pedidos' => $orders->count(),
            'Compras acumuladas' => 'S/ ' . number_format($orders->sum('total'), 2),
            'Última compra' => optional($orders->first()?->created_at)->format('d/m/Y H:i') ?? 'Sin registros',
        ]);

        if ($orders->isEmpty()) {
            $pdf->addParagraph('No hay pedidos asociados a este cliente.');
            return $pdf->download('pedidos-cliente-' . $customer->id . '.pdf');
        }

        $rows = $orders->map(function ($order) {
            return [
                '#' . $order->id,
                $order->items->sum('quantity'),
                'S/ ' . number_format($order->total, 2),
                \App\Support\OrderStatus::label($order->status),
                optional($order->created_at)->format('d/m/Y H:i'),
            ];
        })->all();

        $pdf->addTable([
            'Pedido', 'Productos', 'Total', 'Estado', 'Fecha'
        ], $rows, [70, 80, 90, 100, 140]);

        return $pdf->download('pedidos-cliente-' . $customer->id . '.pdf');
    }

    public function update(Request $request, User $customer)
    {
        abort_if($customer->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customer->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('ok', 'Perfil de cliente actualizado.');
    }
}
