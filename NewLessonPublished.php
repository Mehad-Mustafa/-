<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLessonPublished extends Notification
{
    use Queueable;

    public function __construct(
        public Lesson $lesson,
        public Course $course
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->notify_emails) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("درس جديد في كورس: {$this->course->title}")
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تمت إضافة درس جديد إلى كورس اشتركت فيه:")
            ->line("**الكورس:** {$this->course->title}")
            ->line("**الدرس:** {$this->lesson->title}")
            ->action('مشاهدة الدرس', route('student.lessons.show', [$this->course, $this->lesson]))
            ->salutation('منصة الكورسات');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'new_lesson',
            'icon'    => 'book',
            'color'   => 'royal',
            'title'   => 'درس جديد متاح',
            'message' => "أُضيف درس جديد: {$this->lesson->title}",
            'meta'    => $this->course->title,
            'url'     => route('student.lessons.show', [$this->course, $this->lesson]),
        ];
    }
}
