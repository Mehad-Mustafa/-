@csrf
@isset($course)
    @method('PUT')
@endisset

<div class="space-y-6">
    {{-- العنوان --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الكورس <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $course->title ?? '') }}" required
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-500">
    </div>

    {{-- الوصف --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">تفاصيل الكورس <span class="text-red-500">*</span></label>
        <textarea name="description" rows="6" required
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-500">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

    {{-- الصورة --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">صورة الكورس</label>

        @isset($course)
            @if($course->image)
                <div class="mb-3">
                    <img src="{{ $course->image_url }}" alt="" class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-500 mt-1">الصورة الحالية</p>
                </div>
            @endif
        @endisset

        <input type="file" name="image" accept="image/*" id="image-input"
               class="w-full text-sm text-gray-500 file:ml-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-royal-50 file:text-royal-700 hover:file:bg-royal-100">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP - أقصى 2 ميجا</p>

        <div id="image-preview" class="mt-3 hidden">
            <img id="preview-img" src="" alt="" class="w-48 h-32 object-cover rounded-lg border border-royal-300">
            <p class="text-xs text-royal-600 mt-1">معاينة الصورة الجديدة</p>
        </div>
    </div>

    {{-- 🆕 الفيديو التعريفي --}}
    <div class="border-2 border-dashed border-royal-200 bg-royal-50/30 rounded-xl p-5">
        <label class="block text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
            🎬 الفيديو التعريفي للكورس
            <span class="text-xs font-normal text-gray-500">(اختياري)</span>
        </label>
        <p class="text-xs text-gray-600 mb-4">فيديو قصير يظهر في صفحة الكورس لتعريف الطلاب بمحتواه قبل البدء.</p>

        @isset($course)
            @if($course->intro_video_type === 'uploaded')
                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    ✓ يوجد فيديو تعريفي مرفوع
                </div>
            @elseif($course->intro_video_type === 'external')
                <div class="mb-3 p-3 bg-royal-50 border border-royal-200 rounded-lg text-sm text-royal-700">
                    🔗 يوجد رابط فيديو تعريفي: <span dir="ltr" class="font-mono">{{ \Illuminate\Support\Str::limit($course->intro_video_url, 50) }}</span>
                </div>
            @endif
        @endisset

        {{-- Tabs --}}
        <div class="flex gap-2 mb-4">
            <button type="button" onclick="switchIntroTab('url')" id="intro-tab-url"
                    class="flex-1 px-4 py-2 text-sm font-medium rounded-lg bg-royal-100 text-royal-700">
                🔗 رابط خارجي
            </button>
            <button type="button" onclick="switchIntroTab('upload')" id="intro-tab-upload"
                    class="flex-1 px-4 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-700">
                📁 رفع ملف
            </button>
        </div>

        <div id="intro-url-section">
            <input type="url" name="intro_video_url" value="{{ old('intro_video_url', $course->intro_video_url ?? '') }}"
                   placeholder="https://www.youtube.com/watch?v=... أو https://vimeo.com/..."
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-500" dir="ltr">
            <p class="text-xs text-gray-500 mt-1">يدعم YouTube و Vimeo</p>
        </div>

        <div id="intro-upload-section" class="hidden">
            <input type="file" name="intro_video_file" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                   class="w-full text-sm text-gray-500 file:ml-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-royal-50 file:text-royal-700 hover:file:bg-royal-100">
            <p class="text-xs text-gray-500 mt-1">MP4, MOV, AVI, WEBM - أقصى 5 جيجابايت (مدة قصوى: 25 دقيقة)</p>
        </div>
    </div>

    <div class="flex gap-3 pt-4 border-t border-gray-200">
        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-royal-700 to-royal-600 hover:from-royal-800 hover:to-royal-700 text-white font-medium rounded-lg shadow transition">
            {{ isset($course) ? 'حفظ التعديلات' : 'إنشاء الكورس' }}
        </button>
        <a href="{{ route('admin.courses.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
            إلغاء
        </a>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function switchIntroTab(tab) {
    const urlSection = document.getElementById('intro-url-section');
    const uploadSection = document.getElementById('intro-upload-section');
    const tabUrl = document.getElementById('intro-tab-url');
    const tabUpload = document.getElementById('intro-tab-upload');

    if (tab === 'url') {
        urlSection.classList.remove('hidden');
        uploadSection.classList.add('hidden');
        tabUrl.classList.add('bg-royal-100', 'text-royal-700');
        tabUrl.classList.remove('bg-gray-100', 'text-gray-700');
        tabUpload.classList.add('bg-gray-100', 'text-gray-700');
        tabUpload.classList.remove('bg-royal-100', 'text-royal-700');
    } else {
        urlSection.classList.add('hidden');
        uploadSection.classList.remove('hidden');
        tabUpload.classList.add('bg-royal-100', 'text-royal-700');
        tabUpload.classList.remove('bg-gray-100', 'text-gray-700');
        tabUrl.classList.add('bg-gray-100', 'text-gray-700');
        tabUrl.classList.remove('bg-royal-100', 'text-royal-700');
    }
}

@isset($course)
    @if($course->intro_video_type === 'uploaded')
        document.addEventListener('DOMContentLoaded', () => switchIntroTab('upload'));
    @endif
@endisset
</script>
@endpush
