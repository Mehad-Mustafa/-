<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * الصفحة الرئيسية / - تحوّل دائماً
     * - إذا غير مسجّل: للـ Login
     * - إذا مسجّل أدمن: للوحة الإدارة
     * - إذا طالب مسجّل: للوحة الطالب
     */
    public function index(): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return $this->redirect();
    }

    /**
     * توجيه المستخدم المسجّل حسب دوره
     */
    public function redirect(): RedirectResponse
    {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('student.dashboard');
    }
}
