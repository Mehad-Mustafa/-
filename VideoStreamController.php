<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Notifications\StudentDownloadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    /**
     * بث فيديو الدرس مع دعم Range Requests
     */
    public function stream(Request $request, Lesson $lesson)
    {
        if (!$lesson->video_path || !Storage::disk('videos')->exists($lesson->video_path)) {
            abort(404, 'الفيديو غير موجود');
        }

        return $this->streamFile(
            $request,
            Storage::disk('videos')->path($lesson->video_path)
        );
    }

    /**
     * بث الفيديو التعريفي للكورس
     */
    public function streamCourseIntro(Request $request, Course $course)
    {
        if (!$course->intro_video_path || !Storage::disk('videos')->exists($course->intro_video_path)) {
            abort(404, 'الفيديو التعريفي غير موجود');
        }

        return $this->streamFile(
            $request,
            Storage::disk('videos')->path($course->intro_video_path)
        );
    }

    /**
     * تحميل فيديو الدرس (إذا كان قابل للتحميل)
     */
    public function download(Lesson $lesson)
    {
        if (!$lesson->downloadable) {
            abort(403, 'هذا الفيديو غير متاح للتحميل');
        }

        if (!$lesson->video_path || !Storage::disk('videos')->exists($lesson->video_path)) {
            abort(404, 'الفيديو غير موجود');
        }

        $path = Storage::disk('videos')->path($lesson->video_path);
        $filename = $this->safeFilename($lesson->title) . '.mp4';

        $this->notifyAdminsAboutDownload($lesson->title, 'lesson');

        return response()->download($path, $filename);
    }

    /**
     * تحميل الفيديو التعريفي للكورس
     */
    public function downloadCourseIntro(Course $course)
    {
        if (!$course->intro_video_path || !Storage::disk('videos')->exists($course->intro_video_path)) {
            abort(404, 'الفيديو التعريفي غير موجود');
        }

        $path = Storage::disk('videos')->path($course->intro_video_path);
        $filename = $this->safeFilename($course->title) . '_intro.mp4';

        $this->notifyAdminsAboutDownload($course->title, 'intro');

        return response()->download($path, $filename);
    }

    private function notifyAdminsAboutDownload(string $fileName, string $fileType): void
    {
        $student = Auth::user();
        if (!$student) return;

        $admins = User::role('admin')->where('notify_admin_new_download', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new StudentDownloadedFile($student, $fileName, $fileType));
        }
    }

    /**
     * Helper: بث ملف مع Range support
     */
    private function streamFile(Request $request, string $path): StreamedResponse
    {
        $size = filesize($path);
        $start = 0;
        $end = $size - 1;
        $length = $size;
        $status = 200;
        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($request->hasHeader('Range')) {
            $range = $request->header('Range');

            if (preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
                $start = (int) $matches[1];
                $end = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : $size - 1;

                if ($start > $end || $start >= $size) {
                    abort(416, 'Requested Range Not Satisfiable');
                }

                $length = $end - $start + 1;
                $status = 206;
                $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            }
        }

        $headers['Content-Length'] = $length;

        return new StreamedResponse(function () use ($path, $start, $length) {
            $stream = fopen($path, 'rb');
            fseek($stream, $start);

            $bufferSize = 8192;
            $remaining = $length;

            while ($remaining > 0 && !feof($stream) && !connection_aborted()) {
                $readLength = min($bufferSize, $remaining);
                echo fread($stream, $readLength);
                $remaining -= $readLength;
                flush();
            }

            fclose($stream);
        }, $status, $headers);
    }

    /**
     * تنظيف اسم الملف من الأحرف غير المسموحة
     */
    private function safeFilename(string $name): string
    {
        return preg_replace('/[^\p{L}\p{N}\s_-]/u', '', $name) ?: 'video';
    }
}
