<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'notifications');
        $user = Auth::user();

        return view('admin.settings.index', compact('user', 'tab'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'notify_admin_new_student' => $request->boolean('notify_admin_new_student'),
            'notify_admin_new_download' => $request->boolean('notify_admin_new_download'),
            'notify_admin_emails'       => $request->boolean('notify_admin_emails'),
        ]);

        return redirect()
            ->route('admin.settings.index', ['tab' => 'notifications'])
            ->with('success', 'تم تحديث إعدادات الإشعارات');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.settings.index', ['tab' => 'security'])
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
