<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $producer;

    public function __construct(Product $product, User $producer)
    {
        $this->product = $product;
        $this->producer = $producer;
    }

    public function build()
    {
        return $this->subject("Alerta de Stock Bajo - {$this->product->name} - AgroConecta")
            ->view('emails.low-stock-alert');
    }
}