<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class VideoUrlController extends Controller
{
    /**
     * تجديد signed URL للفيديو قبل انتهاء صلاحيته
     */
    public function refresh(Lesson $lesson): JsonResponse
    {
        if ($lesson->video_type !== 'uploaded') {
            return response()->json(['error' => 'Not an uploaded video'], 400);
        }

        return response()->json([
            'stream_url' => $lesson->secure_stream_url,
            'expires_in' => (int) env('VIDEO_SIGNED_URL_LIFETIME', 60) * 60, // seconds
        ]);
    }
}
