<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos por Categoría - Cooperativa</title>
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

        .filters-box {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #10B981;
        }

        .filters-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
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
            display: flex;
            justify-content: center;
            align-items: center;
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
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #E5E7EB;
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

        .money {
            color: #059669;
            font-weight: 700;
            font-size: 13px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 10px;
            color: #6B7280;
        }

        .highlight-row {
            background: linear-gradient(90deg, #D1FAE5 0%, #ECFDF5 100%) !important;
            font-weight: bold;
        }

        .summary-box {
            background: #ECFDF5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #10B981;
        }

        .summary-box h3 {
            color: #065F46;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #D1FAE5;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            color: #065F46;
        }

        .category-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .totals-row {
            background: #1F2937 !important;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .totals-row td {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1> Pedidos por Categoría - Cooperativa</h1>
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

    @if($categoryId)
    <div class="filters-box">
        <h3>Filtros Aplicados</h3>
        <p><strong>Categoría:</strong>
            @php
            $category = \App\Models\Category::find($categoryId);
            @endphp
            {{ $category ? $category->name : 'Todas las categorías' }}
        </p>
    </div>
    @endif

    @if($hasChart && isset($chartUrl))
    <div class="chart-section">
        <h2>Distribución por Categoría</h2>
        <div class="chart-container">
            <img src="{{ $chartUrl }}" alt="Gráfica de pastel - Distribución por categoría">
        </div>
    </div>
    @endif

    <div class="table-container">
        <h2 style="margin-bottom: 10px; color: #1F2937;">
            Resumen por Categoría ({{ count($ordersByCategory) }} categorías)
        </h2>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="text-center">Total de Pedidos</th>
                    <th class="text-center">Cantidad Total</th>
                    <th class="text-right">Costo Total</th>
                    <th class="text-right">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalOrders = 0;
                $totalQuantity = 0;
                $totalCost = 0;

                foreach($ordersByCategory as $cat) {
                $totalOrders += $cat->total_orders;
                $totalQuantity += $cat->total_quantity;
                $totalCost += $cat->total_cost;
                }
                @endphp

                @foreach($ordersByCategory as $index => $category)
                @php
                $percentage = $totalCost > 0 ? ($category->total_cost / $totalCost) * 100 : 0;
                $isTopCategory = $index < 3;
                    @endphp

                    <tr class="{{ $isTopCategory ? 'highlight-row' : '' }}">
                    <td class="font-bold">
                        {{ $category->display_category }}
                        @if($index === 0)
                        <span style="color: #F59E0B;">⭐</span>
                        @endif
                    </td>
                    <td class="text-center" style="color: #3B82F6; font-weight: bold;">{{ $category->total_orders }}</td>
                    <td class="text-center">{{ $category->total_quantity }}</td>
                    <td class="text-right money">${{ number_format($category->total_cost, 2) }}</td>
                    <td class="text-right" style="color: #8B5CF6; font-weight: bold;">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                    @endforeach

                    <tr class="totals-row">
                        <td>TOTALES</td>
                        <td class="text-center">{{ $totalOrders }}</td>
                        <td class="text-center">{{ $totalQuantity }}</td>
                        <td class="text-right">${{ number_format($totalCost, 2) }}</td>
                        <td class="text-right">100%</td>
                    </tr>
            </tbody>
        </table>
    </div>

    @if(count($ordersByCategory) > 0)
    <div class="summary-box">
        <h3>📊 Análisis de Distribución</h3>
        <div class="summary-item">
            <span>Categoría más demandada:</span>
            <span class="font-bold">{{ $ordersByCategory[0]->display_category }} ({{ $ordersByCategory[0]->total_orders }} pedidos)</span>
        </div>
        <div class="summary-item">
            <span>Mayor generación de ingresos:</span>
            <span class="font-bold">${{ number_format($ordersByCategory->max('total_cost'), 2) }}</span>
        </div>
        <div class="summary-item">
            <span>Promedio por categoría:</span>
            <span class="font-bold">${{ number_format($totalCost / count($ordersByCategory), 2) }}</span>
        </div>
        <div class="summary-item">
            <span>Total categorías activas:</span>
            <span class="font-bold">{{ count($ordersByCategory) }}</span>
        </div>
        <div class="summary-item">
            <span>TOTAL GENERAL:</span>
            <span class="money" style="font-size: 16px;">${{ number_format($totalCost, 2) }}</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema.</p>
        <p>{{ config('app.name') }} © {{ date('Y') }}</p>
    </div>
</body>

</html>