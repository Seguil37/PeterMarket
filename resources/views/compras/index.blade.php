<h2>Lista de Compras</h2>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

<form action="/compras" method="POST">
    @csrf
    <label>Producto:</label>
    <input type="text" name="producto" required><br>

    <label>Cantidad:</label>
    <input type="number" name="cantidad" required><br>

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" required><br>

    <button type="submit">Agregar Compra</button>
</form>

<hr>

<table border="1" cellpadding="5">
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Total</th>
    </tr>
    @foreach($compras as $c)
    <tr>
        <td>{{ $c->producto }}</td>
        <td>{{ $c->cantidad }}</td>
        <td>{{ $c->precio }}</td>
        <td>{{ $c->total }}</td>
    </tr>
    @endforeach
</table>
