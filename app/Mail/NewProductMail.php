<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProductMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $user;

    public function __construct(Product $product, User $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject("Nuevo Producto Disponible - {$this->product->name} - AgroConecta")
            ->view('emails.new-product');
    }
}