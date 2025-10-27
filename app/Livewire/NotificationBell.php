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
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }
}
