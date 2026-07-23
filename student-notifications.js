document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('studentNotificationsWidget');

    if (!widget) {
        return;
    }

    const badge = document.getElementById('studentNotificationsBadge');
    const toggle = document.getElementById('studentNotificationsToggle');
    const dropdown = document.getElementById('studentNotificationsDropdown');
    const menu = document.getElementById('studentNotificationsMenu');
    const markAll = document.getElementById('studentNotificationsMarkAll');
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');

    if (!badge || !toggle || !dropdown || !menu || !markAll || !tokenMeta) {
        return;
    }

    const token = tokenMeta.getAttribute('content');
    const recentUrl = widget.dataset.notificationsRecentUrl;
    const countUrl = widget.dataset.notificationsCountUrl;
    const readAllUrl = widget.dataset.notificationsReadAllUrl;
    const readBaseUrl = widget.dataset.notificationsReadUrl;

    async function loadNotifications() {
        try {
            const [recentRes, countRes] = await Promise.all([
                fetch(recentUrl),
                fetch(countUrl),
            ]);

            const recent = await recentRes.json();
            const count = await countRes.json();

            badge.textContent = count.count;
            badge.classList.toggle('hidden', count.count === 0);

            if (!recent.length) {
                menu.innerHTML = '<div class="p-4 text-sm text-gray-500">لا توجد إشعارات جديدة.</div>';
                return;
            }

            menu.innerHTML = recent.map(item => {
                return `
                    <a href="${item.data.url ?? '#'}" data-notification-id="${item.id}" class="block px-4 py-3 hover:bg-gray-50 text-sm ${item.read ? 'text-gray-600' : 'text-gray-900 font-semibold'}">
                        <div class="flex justify-between items-center gap-3">
                            <div class="truncate">${item.data.title ?? 'إشعار جديد'}</div>
                            <span class="text-[11px] text-gray-400">${item.time}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 truncate">${item.data.message ?? ''}</p>
                    </a>
                `;
            }).join('');

            menu.querySelectorAll('a[data-notification-id]').forEach(link => {
                link.addEventListener('click', async function (event) {
                    event.preventDefault();
                    const id = this.dataset.notificationId;
                    const href = this.href;

                    try {
                        await fetch(`${readBaseUrl}/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                            body: JSON.stringify({}),
                        });
                    } catch (error) {
                        console.error(error);
                    }

                    window.location.href = href;
                });
            });
        } catch (error) {
            menu.innerHTML = '<div class="p-4 text-sm text-red-500">حدث خطأ أثناء تحميل الإشعارات.</div>';
            console.error(error);
        }
    }

    function toggleDropdown() {
        dropdown.classList.toggle('hidden');
    }

    toggle.addEventListener('click', function () {
        toggleDropdown();
    });

    markAll.addEventListener('click', async function () {
        try {
            await fetch(readAllUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({}),
            });
            await loadNotifications();
        } catch (error) {
            console.error(error);
        }
    });

    document.addEventListener('click', function (event) {
        if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    loadNotifications();
});
