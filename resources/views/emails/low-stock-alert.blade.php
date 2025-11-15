<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f59e0b;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9f9f9;
        }

        .alert-box {
            background: #fef3c7;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }

        .product-details {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
        }

        .stock-indicator {
            font-size: 48px;
            font-weight: bold;
            color: #dc2626;
            text-align: center;
            margin: 15px 0;
        }

        .warning-badge {
            background: #dc2626;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
        }

        .recommendation {
            background: #e0f2fe;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #0ea5e9;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }

        .cta-button {
            display: inline-block;
            background: #f59e0b;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Alerta de Stock Bajo</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $producer->name }}</strong>,</p>

            <div class="warning-badge">
                Acción Requerida
            </div>

            <div class="alert-box">
                <p style="font-size: 16px; margin: 0;">
                    <strong>Uno de tus productos está alcanzando el nivel mínimo de inventario.</strong>
                </p>
            </div>

            <div class="product-details">
                <h3>Información del Producto</h3>
                <p><strong>Producto:</strong> {{ $product->name }}</p>
                <p><strong>SKU:</strong> {{ $product->sku ?? 'N/A' }}</p>
                <p><strong>Categoría:</strong> {{ $product->category->name ?? 'Sin categoría' }}</p>

                <div class="stock-indicator">
                    {{ $product->stock_quantity }}
                </div>
                <p style="text-align: center; color: #dc2626; font-weight: bold;">
                    Unidades Restantes
                </p>

                <p><strong>Precio actual:</strong> ${{ number_format($product->price, 2) }}</p>
                <p><strong>Estado:</strong>
                    <span style="color: {{ $product->is_available ? '#22c55e' : '#dc2626' }};">
                        {{ $product->is_available ? 'Disponible' : 'No disponible' }}
                    </span>
                </p>
            </div>

            <div class="recommendation">
                <h4 style="margin-top: 0;">Recomendación</h4>
                <p>
                    Para mantener tu inventario activo y evitar pérdida de ventas, te recomendamos:
                </p>
                <ul>
                    <li>Reabastecer el inventario lo antes posible</li>
                    <li>Actualizar el stock en el sistema una vez recibas nueva mercancía</li>
                    <li>Considerar aumentar el nivel de reabastecimiento si este producto tiene alta demanda</li>
                </ul>
            </div>

            <center>
                <a href="{{ config('app.url') }}/products/{{ $product->id }}/edit" class="cta-button">
                    Actualizar Inventario
                </a>
            </center>

            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                <strong>Nota:</strong> Si el stock llega a 0, el producto se marcará como "No disponible" automáticamente.
            </p>
        </div>

        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>

</html>