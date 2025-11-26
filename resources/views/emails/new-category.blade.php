<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #8b5cf6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .category-card { 
            background: white; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .new-badge {
            background: #8b5cf6;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
        }
        .category-icon {
            font-size: 64px;
            text-align: center;
            margin: 20px 0;
        }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .cta-button { 
            display: inline-block; 
            background: #8b5cf6; 
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
            <h1>¡Nueva Categoría Disponible!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <div class="new-badge">
                Recién Agregada
            </div>
            
            <p>¡Tenemos buenas noticias! Se ha habilitado una nueva categoría de productos en AgroConecta:</p>
            
            <div class="category-card">
                <div class="category-icon">
                    
                </div>
                
                <h2 style="text-align: center; color: #8b5cf6; margin: 15px 0;">
                    {{ $category->name }}
                </h2>
                
                @if($category->description)
                    <p style="text-align: center; color: #666; margin: 20px 0;">
                        {{ $category->description }}
                    </p>
                @endif
                
                <p style="text-align: center; font-size: 14px; color: #666;">
                    Explora los nuevos productos disponibles en esta categoría
                </p>
            </div>
            
            <center>
                <a href="{{ config('app.url') }}/products?category={{ $category->id }}" class="cta-button">
                    Explorar Categoría
                </a>
            </center>
            
            <p style="margin-top: 20px;">Estamos ampliando constantemente nuestro catálogo para ofrecerte la mejor variedad de productos.</p>
        </div>
        
        <div class="footer">
            <p>AgroConecta © {{ date('Y') }}</p>
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>