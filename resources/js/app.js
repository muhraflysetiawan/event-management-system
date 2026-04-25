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
        // Poll every 30 seconds
        setTimeout(() => this.fetchCount(), 30000);
    }
}));

Alpine.start();
