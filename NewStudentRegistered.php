<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewStudentRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $student) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_admin_emails) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طالب جديد انضم إلى المنصة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("انضم طالب جديد إلى المنصة:")
            ->line("**الاسم:** {$this->student->name}")
            ->line("**البريد:** {$this->student->email}")
            ->action('عرض الطالب', route('admin.students.show', $this->student))
            ->salutation('منصة الكورسات');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'new_student',
            'icon'    => 'user-plus',
            'color'   => 'blue',
            'title'   => 'طالب جديد انضم للمنصة',
            'message' => "سجّل {$this->student->name} حساباً جديداً",
            'meta'    => $this->student->email,
            'url'     => route('admin.students.show', $this->student),
        ];
    }
}
