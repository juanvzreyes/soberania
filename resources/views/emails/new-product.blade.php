<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .product-card { 
            background: white; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .new-badge {
            background: #3b82f6;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
        }
        .price {
            font-size: 28px;
            font-weight: bold;
            color: #22c55e;
            margin: 15px 0;
        }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .cta-button { 
            display: inline-block; 
            background: #3b82f6; 
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
            <h1>¡Nuevo Producto Disponible!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <div class="new-badge">
                Recién Agregado
            </div>
            
            <p>Nos complace informarte que hay un nuevo producto disponible en AgroConecta:</p>
            
            <div class="product-card">
                @if($product->photos && $product->photos->first())
                    <img src="{{ $product->photos->first()->url }}" alt="{{ $product->name }}" class="product-image">
                @endif
                
                <h2 style="margin: 15px 0;">{{ $product->name }}</h2>
                
                <p><strong>Categoría:</strong> {{ $product->category->name ?? 'Sin categoría' }}</p>
                
                @if($product->description)
                    <p><strong>Descripción:</strong> {{ $product->description }}</p>
                @endif
                
                <div class="price">
                    ${{ number_format($product->price, 2) }}
                </div>
                
                <p><strong>Stock disponible:</strong> {{ $product->stock_quantity }} unidades</p>
                
                @if($product->user)
                    <p><strong>Productor:</strong> {{ $product->user->name }}</p>
                @endif
            </div>
            
            <center>
                <a href="{{ config('app.url') }}/products/{{ $product->id }}" class="cta-button">
                    Ver Producto
                </a>
            </center>
            
            <p style="margin-top: 20px;">¡No te pierdas esta oportunidad de adquirir productos frescos y de calidad!</p>
        </div>
        
        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>