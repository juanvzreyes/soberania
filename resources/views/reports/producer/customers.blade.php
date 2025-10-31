<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
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

        .chart-container {
            text-align: center;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .chart-container img {
            max-width: 90%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reporte de Top Clientes</h1>
        <p><strong>Periodo:</strong> {{ $startDate ?? 'Inicio' }} a {{ $endDate ?? 'Fin' }}</p>

        @if (!empty($chartUrl))
        <div class="chart-container">
            <h2>Top Clientes por Consumo</h2>
            <img src="{{ $chartUrl }}" alt="Gráfica de Clientes">
        </div>
        @endif

        <h2>Detalle de Clientes</h2>
        @if($topCustomers->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Total Gastado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topCustomers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>${{ number_format($customer->total_spent ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>No se encontraron clientes en el periodo seleccionado.</p>
        @endif

    </div>
</body>

</html>