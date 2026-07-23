import './bootstrap';
import './student-notifications';

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    document.querySelectorAll('[onclick*="classList.toggle"]').forEach(button => {
        if (!button.contains(e.target) && !button.nextElementSibling?.contains(e.target)) {
            button.nextElementSibling?.classList.add('hidden');
        }
    });
});
