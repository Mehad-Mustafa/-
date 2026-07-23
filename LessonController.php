<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;

class LessonController extends Controller
{
    /**
     * عرض درس - متاح لكل المسجلين
     */
    public function show(Course $course, Lesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $course->load(['lessons' => fn($q) => $q->orderBy('order')]);

        $previousLesson = $lesson->previousLesson();
        $nextLesson = $lesson->nextLesson();

        return view('student.lessons.show', compact(
            'course',
            'lesson',
            'previousLesson',
            'nextLesson'
        ));
    }
}
