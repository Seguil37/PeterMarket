<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Compra;

class CompraController extends Controller
{
    // Guardar compra (por ahora manual)
    public function store(Request $request)
    {
        Compra::create([
            'producto' => $request->producto,
            'cantidad' => $request->cantidad,
            'precio'   => $request->precio,
            'total'    => $request->cantidad * $request->precio,
        ]);

        return redirect()->back()->with('success', 'Compra registrada');
    }

    // Mostrar todas las compras
    public function index()
    {
        $compras = Compra::all();
        return view('compras.index', compact('compras'));
    }
}

