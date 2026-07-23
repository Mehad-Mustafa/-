<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'notify_courses',
        'notify_emails',
        'notify_admin_new_student',
        'notify_admin_new_download',
        'notify_admin_emails',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_courses' => 'boolean',
            'notify_emails' => 'boolean',
            'notify_admin_new_student' => 'boolean',
            'notify_admin_new_download' => 'boolean',
            'notify_admin_emails' => 'boolean',
        ];
    }

    /**
     * هل المستخدم Admin؟
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Accessor: رابط الصورة الشخصية مع fallback ذكي
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
                    return Storage::disk('public')->url($this->avatar);
                }

                $letter = mb_strtoupper(mb_substr($this->name ?? 'U', 0, 1));
                $colors = ['3b82f6', '8b5cf6', 'ec4899', 'f59e0b', '10b981', '06b6d4'];
                $color = $colors[abs(crc32($this->email ?? '')) % count($colors)];

                return "https://ui-avatars.com/api/?name={$letter}&background={$color}&color=fff&size=200&bold=true";
            }
        );
    }

    /**
     * هل لدى المستخدم صورة شخصية مرفوعة؟
     */
    public function hasAvatar(): bool
    {
        return $this->avatar && Storage::disk('public')->exists($this->avatar);
    }

    /**
     * الكورسات المحفوظة (Playlist)
     */
    public function savedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    /**
     * هل الكورس محفوظ لدى المستخدم؟
     */
    public function hasSavedCourse(Course $course): bool
    {
        return $this->savedCourses()->where('course_id', $course->id)->exists();
    }
}
