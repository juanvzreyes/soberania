<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        h2 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        .low-stock {
            color: #D9534F;
            font-weight: bold;
        }

        .no-stock {
            color: #000;
            background-color: #f2f2f2;
        }

        .chart-container {
            text-align: center;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 90%;
            height: auto;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reporte de Inventario</h1>

        @if (!empty($chartUrl))
        <div class="chart-container">
            <h2>Proporción de Productos Vendidos (Top 10)</h2>
            <img src="{{ $chartUrl }}" alt="Gráfica de Productos">
        </div>
        @endif

        <h2 class="page-break">Productos con Bajo Stock (10 o menos)</h2>
        @if($lowStockProducts->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr class="{{ $product->stock_quantity <= 0 ? 'no-stock' : 'low-stock' }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>No hay productos con bajo stock.</p>
        @endif


        <h2 class="page-break">Productos Más Vendidos (Top 10)</h2>
        @if($mostSoldProducts->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Total Vendido (unidades)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mostSoldProducts as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td>{{ $product->total_sold ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Aún no se han registrado ventas.</p>
        @endif

    </div>
</body>

</html>