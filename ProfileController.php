<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'profile');
        $user = Auth::user();

        // 🚫 حذف tab "avatar" إذا حاول المستخدم الوصول له
        if ($tab === 'avatar') {
            $tab = 'profile';
        }

        return view('student.settings.index', compact('user', 'tab'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($user->email !== $data['email']) {
            $data['email_verified_at'] = null;
        }

        // 🆕 معالجة الأفاتار إذا تم رفعه
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // إزالة avatar من $data إذا لم يُرفع ملف (لتجنب null overwrite)
        if (!$request->hasFile('avatar')) {
            unset($data['avatar']);
        }

        $user->update($data);

        return redirect()
            ->route('student.settings.index', ['tab' => 'profile'])
            ->with('success', '✅ تم تحديث البيانات الشخصية بنجاح');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('student.settings.index', ['tab' => 'security'])
            ->with('success', '🔐 تم تغيير كلمة المرور بنجاح');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'notify_courses' => $request->boolean('notify_courses'),
            'notify_emails' => $request->boolean('notify_emails'),
        ]);

        return redirect()
            ->route('student.settings.index', ['tab' => 'notifications'])
            ->with('success', '🔔 تم تحديث إعدادات الإشعارات');
    }

    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logoutOtherDevices($request->password);

        return redirect()
            ->route('student.settings.index', ['tab' => 'account'])
            ->with('success', '🔒 تم تسجيل الخروج من الأجهزة الأخرى');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', '👋 تم حذف حسابك');
    }
}
