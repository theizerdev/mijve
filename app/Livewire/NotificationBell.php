<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public function markAsRead($notificationId)
    {
        $notification = Notification::where('user_id', auth()->id())->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    #[On('notification-created')]
    public function refreshNotifications()
    {
        // Livewire automáticamente re-renderiza
    }

    public function render()
    {
        // Mostrar solo notificaciones no leídas o las 10 más recientes si hay menos de 10 no leídas
        $unreadNotifications = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $totalNotifications = $unreadNotifications->count();
        
        // Si hay menos de 10 notificaciones no leídas, añadir algunas leídas recientes
        $notifications = $unreadNotifications;
        if ($totalNotifications < 10) {
            $readNotifications = Notification::where('user_id', auth()->id())
                ->whereNotNull('read_at')
                ->orderBy('created_at', 'desc')
                ->take(10 - $totalNotifications)
                ->get();
            
            $notifications = $unreadNotifications->merge($readNotifications)
                ->sortByDesc('created_at')
                ->values();
        }

        $unreadCount = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }
}