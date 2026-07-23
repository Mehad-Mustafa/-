<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'intro_video_url' => ['nullable', 'url', 'max:500'],
            'intro_video_file' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:5242880'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الكورس مطلوب',
            'title.min' => 'العنوان يجب أن يكون 3 أحرف على الأقل',
            'description.required' => 'الوصف مطلوب',
            'description.min' => 'الوصف يجب أن يكون 10 أحرف على الأقل',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'الصورة يجب أن تكون: jpeg, jpg, png, webp',
            'image.max' => 'حجم الصورة لا يجب أن يتجاوز 2 ميجا',
            'intro_video_url.url' => 'رابط الفيديو غير صالح',
            'intro_video_file.mimes' => 'الفيديو يجب أن يكون: mp4, mov, avi, webm',
            'intro_video_file.max' => 'حجم الفيديو لا يجب أن يتجاوز 5 جيجابايت',
        ];
    }
}
