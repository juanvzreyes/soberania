{{-- resources/views/reports/producers.blade.php --}}

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Productores</title>
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

        .producer-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .producer-card h2 {
            margin-top: 0;
            color: #333;
        }

        .location,
        .products {
            margin-top: 10px;
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
        <h1>Reporte de Productores</h1>

        @forelse($producers as $producer)
        <div class="producer-card">
            <h2>{{ $producer->name }}</h2>

            <div class="location">
                <strong>Ubicación:</strong>

                @if($producer->producer && $producer->producer->location)
                {{ $producer->producer->location->street ?? 'Calle no especificada' }},
                {{ $producer->producer->location->exterior_number ?? 'SN' }},
                C.P. {{ $producer->producer->location->postal_code ?? '' }}
                @else
                Ubicación no registrada.
                @endif

            </div>

            <div class="products">
                <strong>Productos que ofrece:</strong>
                @if($producer->products->isNotEmpty())
                <ul>
                    @foreach($producer->products as $product)
                    <li>{{ $product->name }} - ${{ number_format($product->price, 2) }}</li>
                    @endforeach
                </ul>
                @else
                <p>Este productor aún no tiene productos registrados.</p>
                @endif
            </div>
        </div>
        @empty
        <p>No hay productores registrados para mostrar en el reporte.</p>
        @endforelse

        {{-- Contenedor para la Gráfica --}}
        @if (!empty($chartData['url']))
        <div class="chart-container">
            <h2>Gráfica de Productos por Productor</h2>
            <img src="{{ $chartData['url'] }}" alt="Gráfica de Productos">
        </div>
        @endif
    </div>
</body>

</html>