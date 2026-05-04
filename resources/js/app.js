import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Notification Bell Component
Alpine.data('notificationBell', () => ({
    count: 0,
    async fetchCount() {
        try {
            const response = await fetch('/api/notifications/unread-count');
            const data = await response.json();
            this.count = data.count;
        } catch (e) {
            this.count = 0;
        }
        // Poll every 10 seconds
        setTimeout(() => this.fetchCount(), 10000);
    }
}));

// Password Toggle Component
Alpine.data('passwordToggle', () => ({
    show: false,
    toggle() {
        this.show = !this.show;
    }
}));

Alpine.start();
