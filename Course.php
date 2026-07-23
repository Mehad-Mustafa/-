<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'intro_video_url',
        'intro_video_path',
    ];

    /**
     * Boot - حذف الصورة والفيديو تلقائياً عند حذف الكورس
     */
    protected static function booted(): void
    {
        static::deleting(function (Course $course) {
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            if ($course->intro_video_path && Storage::disk('videos')->exists($course->intro_video_path)) {
                Storage::disk('videos')->delete($course->intro_video_path);
            }
        });
    }

    /**
     * علاقة الكورس مع الدروس
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * المستخدمون الذين حفظوا هذا الكورس
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * هل الكورس محفوظ لدى المستخدم الحالي؟
     */
    public function isSavedByCurrentUser(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        return $this->savedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Accessor: رابط صورة الكورس
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image
                ? Storage::disk('public')->url($this->image)
                : 'https://via.placeholder.com/600x400/3b82f6/ffffff?text=Course'
        );
    }

    /**
     * نوع الفيديو التعريفي
     */
    protected function introVideoType(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->intro_video_path) return 'uploaded';
                if ($this->intro_video_url) return 'external';
                return 'none';
            }
        );
    }

    /**
     * URL آمن لتشغيل الفيديو التعريفي (موقّع)
     */
    protected function introVideoStreamUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->intro_video_type !== 'uploaded') {
                    return null;
                }

                $minutes = (int) env('VIDEO_SIGNED_URL_LIFETIME', 60);

                return URL::temporarySignedRoute(
                    'course.intro.stream',
                    now()->addMinutes($minutes),
                    ['course' => $this->id]
                );
            }
        );
    }

    /**
     * URL آمن لتحميل الفيديو التعريفي (10 دقائق)
     */
    protected function introVideoDownloadUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->intro_video_type !== 'uploaded') {
                    return null;
                }

                return URL::temporarySignedRoute(
                    'course.intro.download',
                    now()->addMinutes(10),
                    ['course' => $this->id]
                );
            }
        );
    }

    /**
     * اسم ملف الصورة للتحميل
     */
    protected function imageDownloadName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image
                ? \Illuminate\Support\Str::slug($this->title) . '.' . pathinfo($this->image, PATHINFO_EXTENSION)
                : null
        );
    }

    /**
     * تحويل YouTube/Vimeo URLs لـ embed URL
     */
    protected function introEmbedUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->intro_video_type !== 'external' || !$this->intro_video_url) {
                    return null;
                }

                $url = $this->intro_video_url;

                if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    return 'https://www.youtube.com/embed/' . $matches[1];
                }

                if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                    return 'https://www.youtube.com/embed/' . $matches[1];
                }

                if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
                    return 'https://player.vimeo.com/video/' . $matches[1];
                }

                return $url;
            }
        );
    }
}
