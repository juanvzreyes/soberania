<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #22c55e; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .order-details { background: white; padding: 15px; margin: 20px 0; border-radius: 8px; }
        .delivery-info { background: #e8f5e9; padding: 15px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #22c55e; }
        .success-badge { background: #22c55e; color: white; padding: 8px 15px; border-radius: 20px; display: inline-block; margin: 10px 0; }
        .product-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .total { font-size: 18px; font-weight: bold; color: #22c55e; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .cta-button { 
            display: inline-block; 
            background: #22c55e; 
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
            <h1>¡Pedido Entregado!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <div class="success-badge">
                Entrega Completada Exitosamente
            </div>
            
            <p>Nos complace informarte que tu pedido ha sido entregado exitosamente.</p>
            
            <div class="order-details">
                <h3>Pedido #{{ $order->id }}</h3>
                <p><strong>Fecha de pedido:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Fecha de entrega:</strong> {{ $order->delivery->actual_delivery_date ? $order->delivery->actual_delivery_date->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> {{ $order->status }}</p>
                
                @if($order->delivery && $order->delivery->transporter)
                <div class="delivery-info">
                    <h4>Información de Entrega</h4>
                    <p><strong>Transportista:</strong> {{ $order->delivery->transporter->name }}</p>
                    <p><strong>Teléfono:</strong> {{ $order->delivery->transporter->phone ?? 'No disponible' }}</p>
                </div>
                @endif
                
                <h4>Productos Entregados:</h4>
                @foreach($order->orderItems as $item)
                    <div class="product-item">
                        <strong>{{ $item->product->name }}</strong><br>
                        Cantidad: {{ $item->quantity }} x ${{ number_format($item->price, 2) }} = ${{ number_format($item->quantity * $item->price, 2) }}
                    </div>
                @endforeach
                
                <div class="total">
                    Total: ${{ number_format($order->total_amount, 2) }}
                </div>
            </div>
            
            <p>Esperamos que disfrutes de tus productos. Si tienes alguna pregunta o comentario sobre tu pedido, no dudes en contactarnos.</p>
            
            <center>
                <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="cta-button">
                    Ver Detalles del Pedido
                </a>
            </center>
            
            <p style="margin-top: 20px;"><strong>¡Gracias por confiar en AgroConecta!</strong></p>
        </div>
        
        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>