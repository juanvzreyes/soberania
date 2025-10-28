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
        .product-item { border-bottom: 1px solid #eee; padding: 10px 0; }
        .total { font-size: 18px; font-weight: bold; color: #22c55e; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Pedido Confirmado!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <p>Hemos recibido tu pedido y está siendo procesado. Aquí están los detalles:</p>
            
            <div class="order-details">
                <h3>Pedido #{{ $order->id }}</h3>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> {{ $order->status }}</p>
                <p><strong>Método de pago:</strong> {{ $order->payment->payment_method }}</p>
                
                <h4>Productos:</h4>
                @foreach($order->orderItems as $item)
                    <div class="product-item">
                        <strong>{{ $item->product->name }}</strong><br>
                        Cantidad: {{ $item->quantity }} x ${{ $item->price }} = ${{ $item->quantity * $item->price }}
                    </div>
                @endforeach
                
                <div class="total">
                    Total: ${{ $order->total_amount }}
                </div>
            </div>
            
            <p>Te notificaremos cuando tu pedido esté listo para entrega.</p>
            <p>¡Gracias por tu compra!</p>
        </div>
        
        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>