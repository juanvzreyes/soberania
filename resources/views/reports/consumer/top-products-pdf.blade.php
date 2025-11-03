<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos Más Comprados</title>
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

        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            font-size: 14px;
        }

        .rank-1 {
            background: linear-gradient(135deg, #FDE047 0%, #FACC15 100%);
            color: #854D0E;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .rank-2 {
            background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%);
            color: #374151;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .rank-3 {
            background: linear-gradient(135deg, #FB923C 0%, #F97316 100%);
            color: #7C2D12;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .rank-other {
            background: #EFF6FF;
            color: #1E40AF;
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

        .highlight-row-1 {
            background: linear-gradient(90deg, #FEF3C7 0%, #FEF9E7 100%) !important;
            font-weight: bold;
        }

        .highlight-row-2 {
            background: linear-gradient(90deg, #F3F4F6 0%, #F9FAFB 100%) !important;
            font-weight: bold;
        }

        .highlight-row-3 {
            background: linear-gradient(90deg, #FED7AA 0%, #FEE5D3 100%) !important;
            font-weight: bold;
        }

        .trophy {
            font-size: 20px;
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
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Productos Más Comprados</h1>
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
    <div style="margin-bottom: 20px; text-align: center;">
        <h2 style="margin-bottom: 15px; color: #1F2937; font-size: 16px;"> Cantidad por Producto</h2>
        <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #E5E7EB;">
            <img src="{{ $chartUrl }}" alt="Gráfica de barras" style="max-width: 100%; height: auto;">
        </div>
    </div>
    @endif
    <div class="table-container">
        <h2 style="margin-bottom: 10px; color: #1F2937;">
            Ranking de Productos (Top {{ count($topProducts) }})
        </h2>
        <table>
            <thead>
                <tr>
                    <th class="text-center">Ranking</th>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th class="text-center">Cantidad Total</th>
                    <th class="text-center">Veces Comprado</th>
                    <th class="text-right">Total Gastado</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalQuantity = 0;
                $totalSpent = 0;
                @endphp

                @foreach($topProducts as $index => $product)
                @php
                $ranking = $index + 1;
                $totalQuantity += $product->total_quantity;
                $totalSpent += $product->total_spent;

                $rowClass = '';
                if ($ranking === 1) $rowClass = 'highlight-row-1';
                elseif ($ranking === 2) $rowClass = 'highlight-row-2';
                elseif ($ranking === 3) $rowClass = 'highlight-row-3';
                @endphp

                <tr class="{{ $rowClass }}">
                    <td class="text-center">
                        <span class="rank-badge rank-{{ $ranking <= 3 ? $ranking : 'other' }}">
                            {{ $ranking }}
                        </span>
                    </td>
                    <td class="font-bold">
                        @if($ranking === 1)
                        <span class="trophy">🥇</span>
                        @elseif($ranking === 2)
                        <span class="trophy">🥈</span>
                        @elseif($ranking === 3)
                        <span class="trophy">🥉</span>
                        @endif
                        {{ $product->name }}
                    </td>
                    <td class="text-gray">{{ $product->code }}</td>
                    <td class="text-gray">{{ $product->category_name ?? 'Sin categoría' }}</td>
                    <td class="text-center font-bold" style="color: #3B82F6;">{{ $product->total_quantity }}</td>
                    <td class="text-center">{{ $product->times_purchased }}</td>
                    <td class="text-right money">${{ number_format($product->total_spent, 2) }}</td>
                </tr>
                @endforeach

                <tr style="background: #1F2937; color: white; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTALES:</td>
                    <td class="text-center">{{ $totalQuantity }}</td>
                    <td class="text-center">-</td>
                    <td class="text-right">${{ number_format($totalSpent, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(count($topProducts) > 0)
    <div class="summary-box">
        <h3>Análisis</h3>
        <p><strong>Producto favorito:</strong> {{ $topProducts[0]->name }} con {{ $topProducts[0]->total_quantity }} unidades compradas</p>
        <p><strong>Mayor inversión:</strong> ${{ number_format($topProducts->max('total_spent'), 2) }} en {{ $topProducts->sortByDesc('total_spent')->first()->name }}</p>
        <p><strong>Total de productos diferentes:</strong> {{ count($topProducts) }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema.</p>
        <p>{{ config('app.name') }} © {{ date('Y') }}</p>
    </div>
</body>

</html>