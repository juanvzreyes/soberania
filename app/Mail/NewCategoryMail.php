<?php

namespace App\Mail;

use App\Models\Category;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCategoryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $category;
    public $user;

    public function __construct(Category $category, User $user)
    {
        $this->category = $category;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject("Nueva Categoría Disponible - {$this->category->name} - AgroConecta")
            ->view('emails.new-category');
    }
}