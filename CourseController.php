<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * عرض كل الكورسات
     */
    public function index(Request $request)
    {
        $query = Course::withCount('lessons');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->latest()->paginate(12)->withQueryString();

        $savedIds = $request->user()->savedCourses()->pluck('courses.id')->all();

        return view('student.courses.index', compact('courses', 'savedIds'));
    }

    /**
     * عرض كورس معين
     */
    public function show(Course $course)
    {
        $course->load(['lessons' => fn($q) => $q->orderBy('order')]);
        $course->loadCount('lessons');

        $isSaved = $course->isSavedByCurrentUser();

        return view('student.courses.show', compact('course', 'isSaved'));
    }

    /**
     * صفحة الكورسات المحفوظة (Playlist)
     */
    public function saved(Request $request)
    {
        $courses = $request->user()
            ->savedCourses()
            ->withCount('lessons')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        $savedIds = $courses->pluck('id')->all();

        return view('student.courses.saved', compact('courses', 'savedIds'));
    }

    /**
     * حفظ / إلغاء حفظ الكورس
     */
    public function toggleSave(Request $request, Course $course)
    {
        $result = $request->user()->savedCourses()->toggle($course->id);

        $saved = ! empty($result['attached']);

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => $saved,
                'message' => $saved ? 'تم حفظ الكورس في قائمتك' : 'تم إلغاء حفظ الكورس',
            ]);
        }

        return back()->with('success', $saved ? 'تم حفظ الكورس في قائمتك' : 'تم إلغاء حفظ الكورس');
    }
}
