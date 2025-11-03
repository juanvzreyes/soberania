<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pedidos - Cooperativa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #F3F4F6;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #10B981;
            text-align: center;
        }

        .stat-label {
            font-size: 9px;
            color: #6B7280;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1F2937;
        }

        .chart-section {
            margin-bottom: 20px;
            text-align: center;
        }

        .chart-section h2 {
            margin-bottom: 15px;
            color: #1F2937;
            font-size: 16px;
        }

        .chart-container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #E5E7EB;
        }

        .chart-container img {
            max-width: 100%;
            height: auto;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #10B981;
            color: white;
        }

        th {
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            padding: 6px 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-gray {
            color: #6B7280;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
        }

        .money {
            color: #059669;
            font-weight: 600;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-pendiente {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-preparacion {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-camino {
            background: #E0E7FF;
            color: #4338CA;
        }

        .status-entregado {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-cancelado {
            background: #FEE2E2;
            color: #991B1B;
        }

        .order-group {
            background-color: #ECFDF5;
            font-weight: bold;
            border-top: 2px solid #10B981;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Reporte de Pedidos - Cooperativa</h1>
        <div class="header-info">
            <div>
                <strong>Cooperativa:</strong> {{ $user->name }}<br>
                <strong>Email:</strong> {{ $user->email }}
            </div>
            <div style="text-align: right;">
                <strong>Periodo:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
                <strong>Generado:</strong> {{ $generatedAt }}
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Pedidos</div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #3B82F6;">
            <div class="stat-label">Total Productos</div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['total_products'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #8B5CF6;">
            <div class="stat-label">Ingresos Totales</div>
            <div class="stat-value money">${{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #F59E0B;">
            <div class="stat-label">Pendientes</div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #059669;">
            <div class="stat-label">Completados</div>
            <div class="stat-value" style="color: #059669;">{{ $stats['completed_orders'] }}</div>
        </div>
    </div>

    @if($hasChart && isset($chartUrl))
    <div class="chart-section">
        <h2>Tendencia de Pedidos</h2>
        <div class="chart-container">
            <img src="{{ $chartUrl }}" alt="Gráfica de tendencia de pedidos">
        </div>
    </div>
    @endif

    <div class="table-container">
        <h2 style="margin-bottom: 10px; color: #1F2937;">Detalle de Pedidos</h2>
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @php
                $currentOrderId = null;
                $orderTotal = 0;
                @endphp

                @foreach($orders as $order)
                @if($currentOrderId !== null && $currentOrderId !== $order->order_id)
                <tr class="order-group">
                    <td colspan="7" class="text-right">Total Orden {{ $orders->where('order_id', $currentOrderId)->first()->order_number }}:</td>
                    <td class="text-right money">${{ number_format($orderTotal, 2) }}</td>
                    <td></td>
                </tr>
                @php $orderTotal = 0; @endphp
                @endif

                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td class="text-gray">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                    <td>{{ $order->consumer_name }}</td>
                    <td>{{ $order->product_name }}</td>
                    <td class="text-gray">{{ $order->category_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $order->quantity }}</td>
                    <td class="text-right">${{ number_format($order->price, 2) }}</td>
                    <td class="text-right money">${{ number_format($order->subtotal, 2) }}</td>
                    <td class="text-center">
                        @php
                        $statusClass = match($order->status) {
                        'Pendiente' => 'status-pendiente',
                        'En preparación' => 'status-preparacion',
                        'En camino' => 'status-camino',
                        'Entregado' => 'status-entregado',
                        'Cancelado' => 'status-cancelado',
                        default => 'status-pendiente'
                        };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $order->status }}</span>
                    </td>
                </tr>

                @php
                $currentOrderId = $order->order_id;
                $orderTotal += $order->subtotal;
                @endphp
                @endforeach

                @if($currentOrderId !== null)
                <tr class="order-group">
                    <td colspan="7" class="text-right">Total Orden {{ $orders->where('order_id', $currentOrderId)->first()->order_number }}:</td>
                    <td class="text-right money">${{ number_format($orderTotal, 2) }}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema.</p>
        <p>{{ config('app.name') }} © {{ date('Y') }}</p>
    </div>
</body>

</html>