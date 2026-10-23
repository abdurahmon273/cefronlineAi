<?php

namespace App\Livewire\User\Notification;

use App\Models\Notification;
use Livewire\Component;
use App\Models\UserNotification;

class IndexLivewire extends Component
{
    public $newNotifications;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->newNotifications = Notification::leftJoin('user_notification', function($join) {
                $join->on('notifications.id', '=', 'user_notification.notification_id')
                     ->where('user_notification.user_id', auth()->id());
            })
            ->select('notifications.*', 'user_notification.read_at')
            ->orderBy('notifications.created_at', 'desc')
            ->get()
            ->map(function($n) {
                $n->pivot = (object) ['read_at' => $n->read_at ?? null];
                unset($n->read_at);
                return $n;
            });
    }

    public function openNotificationModal($notificationId)
    {
        $notification = $this->newNotifications->find($notificationId);
        if ($notification && !optional($notification->pivot)->read_at) {
            $this->markAsRead($notification->id);
        }
        $this->dispatch('open-news-modal', $notification);
    }

    public function markAsRead($notificationId)
    {
        $user = auth()->user();
        if ($user){
            UserNotification::updateOrInsert(
                ['user_id' => $user->id, 'notification_id' => $notificationId],
                ['read_at' => now()]
            );
        }

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.user.notification.index-livewire');
    }
}
