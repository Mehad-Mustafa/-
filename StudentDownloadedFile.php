<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentDownloadedFile extends Notification
{
    use Queueable;

    public function __construct(
        public User    $student,
        public string  $fileName,
        public string  $fileType,
        public ?string $url = null
    ) {}

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
        $typeLabel = $this->fileType === 'lesson' ? 'فيديو درس' : 'فيديو تعريفي';

        return (new MailMessage)
            ->subject('تحميل ملف من طالب')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("قام أحد الطلاب بتحميل ملف:")
            ->line("**الطالب:** {$this->student->name} ({$this->student->email})")
            ->line("**الملف:** {$this->fileName}")
            ->line("**النوع:** {$typeLabel}")
            ->action('عرض الطالب', route('admin.students.show', $this->student))
            ->salutation('منصة الكورسات');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'new_download',
            'icon'    => 'download',
            'color'   => 'amber',
            'title'   => 'تحميل ملف',
            'message' => "قام {$this->student->name} بتحميل: {$this->fileName}",
            'meta'    => $this->fileType === 'lesson' ? 'فيديو درس' : 'فيديو تعريفي',
            'url'     => route('admin.students.show', $this->student),
        ];
    }
}
