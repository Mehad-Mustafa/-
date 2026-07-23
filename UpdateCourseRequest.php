<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
        return (new StoreCourseRequest)->messages();
    }
}
