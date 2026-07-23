<?php

namespace App\Http\Requests;

use App\Rules\ValidateVideoDuration;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:5242880', new ValidateVideoDuration(1500)],
            'duration' => ['nullable', 'integer', 'min:0'],
            'downloadable' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدرس مطلوب',
            'title.min' => 'العنوان يجب أن يكون 3 أحرف على الأقل',
            'video_url.url' => 'رابط الفيديو غير صالح',
            'video_file.mimes' => 'الملف يجب أن يكون: mp4, mov, avi, webm',
            'video_file.max' => 'حجم الفيديو لا يجب أن يتجاوز 5 جيجابايت (مدة قصوى: 25 دقيقة)',
            'duration.integer' => 'المدة يجب أن تكون رقماً (بالثواني)',
        ];
    }
}
