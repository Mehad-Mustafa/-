<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('student');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::role('student')->count(),
            'verified' => User::role('student')->whereNotNull('email_verified_at')->count(),
            'this_month' => User::role('student')->whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    public function show(User $student)
    {
        if (!$student->hasRole('student')) {
            abort(404);
        }

        return view('admin.students.show', compact('student'));
    }

    public function destroy(User $student)
    {
        if (!$student->hasRole('student')) {
            abort(404);
        }

        $name = $student->name;
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', '🗑️ تم حذف الطالب: ' . $name);
    }
}
