<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_path',
        'video_url',
        'duration',
        'order',
        'downloadable',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'order' => 'integer',
            'downloadable' => 'boolean',
        ];
    }

    /**
     * Boot - حذف ملف الفيديو تلقائياً عند حذف الدرس
     */
    protected static function booted(): void
    {
        static::deleting(function (Lesson $lesson) {
            if ($lesson->video_path && Storage::disk('videos')->exists($lesson->video_path)) {
                Storage::disk('videos')->delete($lesson->video_path);
            }
        });
    }

    /**
     * علاقة الدرس مع الكورس
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * نوع الفيديو: مرفوع، رابط خارجي، أو لا يوجد
     */
    protected function videoType(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->video_path) return 'uploaded';
                if ($this->video_url) return 'external';
                return 'none';
            }
        );
    }

    /**
     * URL آمن لتشغيل الفيديو (موقّع لمدة 60 دقيقة)
     */
    protected function secureStreamUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->video_type !== 'uploaded') {
                    return null;
                }

                $minutes = (int) env('VIDEO_SIGNED_URL_LIFETIME', 60);

                return URL::temporarySignedRoute(
                    'video.stream',
                    now()->addMinutes($minutes),
                    ['lesson' => $this->id]
                );
            }
        );
    }

    /**
     * URL آمن لتحميل الفيديو
     */
    protected function secureDownloadUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->video_type !== 'uploaded' || !$this->downloadable) {
                    return null;
                }

                return URL::temporarySignedRoute(
                    'video.download',
                    now()->addMinutes(10),
                    ['lesson' => $this->id]
                );
            }
        );
    }

    /**
     * تحويل YouTube/Vimeo URLs لـ embed URL
     */
    protected function embedUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->video_type !== 'external' || !$this->video_url) {
                    return null;
                }

                $url = $this->video_url;

                // YouTube
                if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    return 'https://www.youtube.com/embed/' . $matches[1];
                }

                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    return 'https://www.youtube.com/embed/' . $matches[1];
                }

                // Vimeo
                if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                    return 'https://player.vimeo.com/video/' . $matches[1];
                }

                return $url;
            }
        );
    }

    /**
     * تنسيق المدة (HH:MM:SS أو MM:SS)
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->duration) return null;

                $hours = floor($this->duration / 3600);
                $minutes = floor(($this->duration % 3600) / 60);
                $seconds = $this->duration % 60;

                if ($hours > 0) {
                    return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
                }

                return sprintf('%d:%02d', $minutes, $seconds);
            }
        );
    }

    /**
     * الدرس السابق
     */
    public function previousLesson(): ?Lesson
    {
        return self::where('course_id', $this->course_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * الدرس التالي
     */
    public function nextLesson(): ?Lesson
    {
        return self::where('course_id', $this->course_id)
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();
    }
}
