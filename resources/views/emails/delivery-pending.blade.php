<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .delivery-info { background: white; padding: 15px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #3b82f6; }
        .highlight { background: #dbeafe; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Entrega Programada</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <p>Te recordamos que tu pedido será entregado pronto.</p>
            
            <div class="delivery-info">
                <h3>Detalles de Entrega - Pedido #{{ $order->id }}</h3>
                
                <div class="highlight">
                    <strong>📅 Fecha estimada:</strong> 
                    {{ $order->delivery->estimated_delivery_date ? $order->delivery->estimated_delivery_date->format('d/m/Y H:i') : 'Por confirmar' }}
                </div>
                
                <p><strong>🚚 Transportista:</strong> {{ $order->delivery->transporter ? $order->delivery->transporter->name : 'Por asignar' }}</p>
                <p><strong>📍 Estado:</strong> {{ $order->delivery->status }}</p>
            </div>
            
            <p>Por favor, asegúrate de estar disponible en la dirección indicada.</p>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </div>
        
        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>