<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
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

        .summary {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .summary h3 {
            margin-top: 0;
        }

        .producer-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .producer-card h3 {
            margin-top: 0;
            color: #333;
        }

        .products ul {
            padding-left: 20px;
            margin: 0;
        }

        .chart-container {
            text-align: center;
            margin-top: 30px;
            page-break-before: always;
        }

        .chart-container img {
            max-width: 90%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reporte de Ventas</h1>
        <p><strong>Periodo:</strong> {{ $startDate ?? 'Inicio' }} a {{ $endDate ?? 'Fin' }}</p>

        <div class="summary">
            <h3>Resumen General</h3>
            <p><strong>Total de Ventas:</strong> ${{ number_format($totalSales, 2) }}</p>
            <p><strong>Número de Pedidos:</strong> {{ $totalOrders }}</p>
        </div>

        <h2>Detalle de Pedidos</h2>
        @forelse($orders as $order)
        <div class="producer-card">
            <h3>Pedido #{{ $order->id }} - {{ $order->created_at->format('d/m/Y') }}</h3>

            {{-- CÓDIGO CORREGIDO AQUÍ --}}
            <p><strong>Cliente:</strong> {{ ($order->consumer?->name ?? $order->cooperative?->name) ?? 'N/A' }}</p>

            <div class="products">
                <strong>Productos:</strong>
                @if($order->orderItems->isNotEmpty())
                <ul>
                    @foreach($order->orderItems as $item)
                    {{-- Usamos withTrashed en el controlador, así que 'product' no debería ser null --}}
                    <li>{{ $item->product->name ?? 'Producto no disponible' }} ({{ $item->quantity }} x ${{ number_format($item->price, 2) }})</li>
                    @endforeach
                </ul>
                @else
                <p>Este pedido no tiene artículos de este productor.</p>
                @endif
            </div>
        </div>
        @empty
        <p>No se encontraron ventas en el periodo seleccionado.</p>
        @endforelse

        @if (!empty($chartUrl))
        <div class="chart-container">
            <h2>Ventas por Día</h2>
            <img src="{{ $chartUrl }}" alt="Gráfica de Ventas">
        </div>
        @endif
    </div>
</body>

</html>