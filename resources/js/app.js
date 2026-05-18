import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
    },
    init() {
        this.fetchCount();
        const userId = document.querySelector('meta[name="user-id"]')?.content;
        if (userId && window.Echo) {
            window.Echo.private('App.Models.User.' + userId)
                .listen('NotificationCreated', (e) => {
                    this.count++;
                });
        }
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

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
