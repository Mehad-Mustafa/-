<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;

class DashboardController extends Controller
{
    public function index()
    {
        $latestCourses = Course::withCount('lessons')
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'courses' => Course::count(),
            'lessons' => Lesson::count(),
        ];

        return view('student.dashboard', compact('latestCourses', 'stats'));
    }
}
