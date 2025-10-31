<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Estadísticas</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Reporte de Estadísticas</h1>
    <p>Periodo: {{ $filters['startDate'] }} al {{ $filters['endDate'] }}</p>

    <h2>Métricas Principales</h2>
    <table>
        <tr>
            <th>Métrica</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>Productores Activos</td>
            <td>{{ $stats['primaryStat'] }}</td>
        </tr>
        <tr>
            <td>Total de Pedidos</td>
            <td>{{ $stats['totalOrders'] }}</td>
        </tr>
        <tr>
            <td>Ventas Globales</td>
            <td>${{ number_format($stats['totalSales'], 2) }}</td>
        </tr>
    </table>

    <h2>Productos Más Demandados</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Unidades Vendidas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stats['topProducts'] as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->total_sold }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">No hay datos de productos para mostrar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>