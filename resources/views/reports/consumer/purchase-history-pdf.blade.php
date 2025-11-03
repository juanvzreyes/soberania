<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compras</title>
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
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
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
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3B82F6;
        }

        .stat-label {
            font-size: 10px;
            color: #6B7280;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #1F2937;
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
            background-color: #3B82F6;
            color: white;
        }

        th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #E5E7EB;
        }

        tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        tbody tr:hover {
            background-color: #EFF6FF;
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

        .order-group {
            background-color: #EFF6FF;
            font-weight: bold;
            border-top: 2px solid #3B82F6;
        }

        .money {
            color: #059669;
            font-weight: 600;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Historial de Compras</h1>
        <div class="header-info">
            <div>
                <strong>Usuario:</strong> {{ $user->name }}<br>
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
            <div class="stat-label">Total de Órdenes</div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #10B981;">
            <div class="stat-label">Total Productos</div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['total_products'] }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #8B5CF6;">
            <div class="stat-label">Total Gastado</div>
            <div class="stat-value money">${{ number_format($stats['total_spent'], 2) }}</div>
        </div>
        <div class="stat-card" style="border-left-color: #F59E0B;">
            <div class="stat-label">Promedio por Orden</div>
            <div class="stat-value" style="color: #F59E0B;">${{ number_format($stats['total_orders'] > 0 ? $stats['total_spent'] / $stats['total_orders'] : 0, 2) }}</div>
        </div>
    </div>
    @if($hasChart && isset($chartUrl))
    <div style="margin-bottom: 20px; text-align: center;">
        <h2 style="margin-bottom: 15px; color: #1F2937; font-size: 16px;">Tendencia de Compras</h2>
        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB;">
            <img src="{{ $chartUrl }}" alt="Gráfica de tendencia" style="max-width: 100%; height: auto;">
        </div>
    </div>
    @endif

    <div class="table-container">
        <h2 style="margin-bottom: 10px; color: #1F2937;">Detalle de Compras</h2>
        <table>
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Código</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                $currentOrderId = null;
                $orderTotal = 0;
                @endphp

                @foreach($purchases as $purchase)
                @if($currentOrderId !== null && $currentOrderId !== $purchase->order_id)
                <tr class="order-group">
                    <td colspan="6" class="text-right">Total Orden {{ $purchases->where('order_id', $currentOrderId)->first()->order_number }}:</td>
                    <td class="text-right money">${{ number_format($orderTotal, 2) }}</td>
                </tr>
                @php $orderTotal = 0; @endphp
                @endif

                <tr>
                    <td>{{ $purchase->order_number }}</td>
                    <td class="text-gray">{{ \Carbon\Carbon::parse($purchase->order_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $purchase->product_name }}</td>
                    <td class="text-gray">{{ $purchase->product_code }}</td>
                    <td class="text-center">{{ $purchase->quantity }}</td>
                    <td class="text-right">${{ number_format($purchase->price, 2) }}</td>
                    <td class="text-right money">${{ number_format($purchase->subtotal, 2) }}</td>
                </tr>

                @php
                $currentOrderId = $purchase->order_id;
                $orderTotal += $purchase->subtotal;
                @endphp
                @endforeach

                @if($currentOrderId !== null)
                <tr class="order-group">
                    <td colspan="6" class="text-right">Total Orden {{ $purchases->where('order_id', $currentOrderId)->first()->order_number }}:</td>
                    <td class="text-right money">${{ number_format($orderTotal, 2) }}</td>
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