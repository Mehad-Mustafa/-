<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use FFMpeg\FFMpeg;

class ValidateVideoDuration implements ValidationRule
{
    private int $maxDurationSeconds;
    private ?string $errorMessage = null;

    public function __construct(int $maxDurationSeconds = 1500)
    {
        // 1500 seconds = 25 minutes
        $this->maxDurationSeconds = $maxDurationSeconds;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            return;
        }

        try {
            $ffmpeg = FFMpeg::create();
            $video = $ffmpeg->open($value->getRealPath());
            $duration = $video->getDuration();

            if ($duration > $this->maxDurationSeconds) {
                $minutes = ceil($this->maxDurationSeconds / 60);
                $fail("مدة الفيديو لا يجب أن تتجاوز {$minutes} دقيقة (المدة الحالية: " . ceil($duration / 60) . " دقيقة)");
            }
        } catch (\Exception $e) {
            // If FFMpeg is not available, skip validation or log the error
            \Log::warning('FFMpeg validation failed: ' . $e->getMessage());
        }
    }
}
