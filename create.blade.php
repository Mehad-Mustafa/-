@extends('layouts.admin')

@section('title', 'إضافة كورس')
@section('header', 'إضافة كورس جديد')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                ✨ إضافة كورس جديد
            </h2>
            <p class="text-sm text-gray-500 mt-1">أدخل تفاصيل الكورس الذي تريد إضافته</p>
        </div>

        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="p-6">
            @include('admin.courses._form')
        </form>
    </div>
</div>
@endsection
