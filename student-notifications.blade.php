<div id="studentNotificationsWidget" class="relative"
     data-notifications-recent-url="{{ route('student.notifications.recent') }}"
     data-notifications-count-url="{{ route('student.notifications.unread-count') }}"
     data-notifications-read-all-url="{{ route('student.notifications.read-all') }}"
     data-notifications-read-url="{{ url('notifications') }}">
    <button id="studentNotificationsToggle" type="button" class="relative flex items-center justify-center w-11 h-11 rounded-full bg-gray-100 hover:bg-gray-200 transition">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span id="studentNotificationsBadge" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-semibold leading-none text-white bg-red-600 rounded-full hidden">0</span>
    </button>

    <div id="studentNotificationsDropdown" class="hidden absolute left-0 mt-2 w-[320px] bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden z-50">
        <div class="px-4 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-gray-900">الإشعارات</h3>
            <button id="studentNotificationsMarkAll" class="text-xs text-royal-600 hover:text-royal-700">تمييز كمقروء</button>
        </div>
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100" id="studentNotificationsMenu" style="max-height:20rem; overflow-y:auto;">
            <div class="p-4 text-sm text-gray-500">جارٍ تحميل الإشعارات...</div>
        </div>
    </div>
</div>

