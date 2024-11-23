<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;
use App\Models\ingredients;

class FoodExpirationNotification extends Notification
{
    use Queueable;

    protected $ingredient;

    public function __construct(Ingredients $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = new MailMessage;
        $message->subject('Notifikasi Status Bahan Makanan')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Status bahan makanan Anda:');

        // Format quantity berdasarkan category
        $quantity = match ($this->ingredient->category) {
            'fruit' => $this->ingredient->quantity . ' pcs',
            'vegetable' => $this->ingredient->quantity . ' kg',
            'livestock' => $this->ingredient->quantity . ' kg',
            'snack' => $this->ingredient->quantity . ' pack',
            'beverage' => $this->ingredient->quantity . ' liters',
            'dry_food' => $this->ingredient->quantity . ' kg',
            'staple_food' => $this->ingredient->quantity . ' kg',
            'seafood' => $this->ingredient->quantity . ' gr',
            'seasonings' => $this->ingredient->quantity . ' gr',
            default => $this->ingredient->quantity . ' units',
        };

        $message->line('Nama Bahan: ' . $this->ingredient->name)
            ->line('Kategori: ' . $this->ingredient->category)
            ->line('Jumlah: ' . $quantity)
            ->line('Tanggal Beli: ' . Carbon::parse($this->ingredient->purchase_date)->format('d M Y'))
            ->line('Tanggal Kadaluarsa: ' . Carbon::parse($this->ingredient->expiry_date)->format('d M Y'))
            ->line('Status: ' . $this->ingredient->status)
            ->action('Lihat Detail', url('http://127.0.0.1:8000/admin/ingredients' . $this->ingredient->user_id))
            ->line('Mohon segera periksa bahan makanan Anda.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ingredient_id' => $this->ingredient->id,
            'name' => $this->ingredient->name,
            'status' => $this->ingredient->status,
        ];
    }
}
