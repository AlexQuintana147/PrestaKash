class PrestaKashNotifications {
    constructor() {
        this.userId = null;
        this.channel = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.reconnectDelay = 1000;
        this.offlineBuffer = [];
        this.isOnline = navigator.onLine;
        this.pollingInterval = null;
        this.pollingDelay = 30000;

        this._bindConnectivityEvents();
    }

    init(userId) {
        this.userId = userId;
        if (!userId) return;

        this._subscribe();
        this._loadUnreadCount();
        this._startPolling();
    }

    _bindConnectivityEvents() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this._flushOfflineBuffer();
            this._reconnect();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
        });
    }

    _subscribe() {
        if (!window.Echo || !this.userId) return;

        try {
            this.channel = window.Echo.channel(`notifications.${this.userId}`);

            this.channel.listen('.notification.sent', (data) => {
                this._handleIncoming(data);
            });

            this.channel.error((error) => {
                console.error('[PrestaKash WS] Channel error:', error);
                this._scheduleReconnect();
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.warn('[PrestaKash WS] Disconnected');
                this._scheduleReconnect();
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('[PrestaKash WS] Connection error:', err);
            });

            console.log(`[PrestaKash WS] Subscribed to notifications.${this.userId}`);
        } catch (e) {
            console.error('[PrestaKash WS] Subscribe failed:', e);
            this._scheduleReconnect();
        }
    }

    _scheduleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.warn('[PrestaKash WS] Max reconnect attempts reached. Falling back to polling.');
            return;
        }

        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts);
        this.reconnectAttempts++;

        setTimeout(() => {
            this._reconnect();
        }, Math.min(delay, 30000));
    }

    _reconnect() {
        if (!window.Echo) return;

        try {
            window.Echo.connector.pusher.connection.connect();
            this.reconnectAttempts = 0;
        } catch (e) {
            this._scheduleReconnect();
        }
    }

    _handleIncoming(data) {
        if (!this.isOnline) {
            this.offlineBuffer.push(data);
            return;
        }

        this._updateBadge(1);
        this._prependToList(data);
        this._playSound();
        this._dispatchEvent(data);
    }

    _flushOfflineBuffer() {
        while (this.offlineBuffer.length > 0) {
            const data = this.offlineBuffer.shift();
            this._handleIncoming(data);
        }
    }

    _loadUnreadCount() {
        axios.get('/notifications/unread-count').then(response => {
            this._updateBadge(response.data.count, true);
        }).catch(err => {
            console.error('[PrestaKash] Failed to load unread count:', err);
        });
    }

    _startPolling() {
        if (this.pollingInterval) clearInterval(this.pollingInterval);

        this.pollingInterval = setInterval(() => {
            this._loadUnreadCount();
        }, this.pollingDelay);
    }

    _updateBadge(increment, absolute) {
        const badges = document.querySelectorAll('[data-notification-badge]');
        badges.forEach(badge => {
            let count;
            if (absolute) {
                count = increment;
            } else {
                const current = parseInt(badge.textContent) || 0;
                count = current + increment;
            }

            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });
    }

    _prependToList(data) {
        const lists = document.querySelectorAll('[data-notification-list]');
        lists.forEach(list => {
            const emptyState = list.querySelector('[data-empty-state]');
            if (emptyState) emptyState.remove();

            const item = this._createNotificationElement(data);
            list.prepend(item);
        });
    }

    _createNotificationElement(data) {
        const div = document.createElement('div');
        div.setAttribute('data-notification-id', data.id);
        div.className = 'px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors cursor-pointer border-b border-gray-100 dark:border-gray-800/60 bg-blue-50/50 dark:bg-blue-900/10';

        const iconMap = {
            'loan': '<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'payment': '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
            'overdue': '<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
            'bell': '<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
        };

        const icon = iconMap[data.icon] || iconMap['bell'];

        div.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 mt-0.5">${icon}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-900 dark:text-white">${this._escapeHtml(data.title)}</p>
                    ${data.body ? `<p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">${this._escapeHtml(data.body)}</p>` : ''}
                    <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1">${this._timeAgo(data.created_at)}</p>
                </div>
                <div class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-2"></div>
            </div>
        `;

        if (data.action_url) {
            div.addEventListener('click', () => {
                this.markAsRead(data.id);
                window.location.href = data.action_url;
            });
        } else {
            div.addEventListener('click', () => {
                this.markAsRead(data.id);
            });
        }

        return div;
    }

    _escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    _timeAgo(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Ahora';
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h`;
        if (seconds < 604800) return `${Math.floor(seconds / 86400)}d`;
        return date.toLocaleDateString();
    }

    _playSound() {
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbsGczIj2LtN/Uq2g5JUF9qNnYs3hFLUN1l8zUvIZaTER0l8/Ww4xdT0FunMHIp39jUkN3ncbQsYRoWD9wl8LLqoNsYEtqjLrFnIFwZFVhh7K+l4Z4bmVbd5G2v5yMf3Rnb4uywpuShYF0aGyMs8KcmIZ5dHBqj7PBnZiGf3d1cGiOscKemoqBeHRxb5GywJ2ain90dG6RscCcm4uDe3Vxb5GxwJybi4N7dXJvkLHAnJuLg3t1cm+QscCcm4uDe3Vy');
            audio.volume = 0.1;
            audio.play().catch(() => {});
        } catch (e) {}
    }

    _dispatchEvent(data) {
        window.dispatchEvent(new CustomEvent('prestakash:notification', { detail: data }));
    }

    markAsRead(notificationId) {
        axios.post(`/notifications/${notificationId}/read`).then(() => {
            const el = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (el) {
                el.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/10');
                const dot = el.querySelector('.bg-blue-500');
                if (dot) dot.remove();
            }
            this._loadUnreadCount();
        }).catch(err => {
            console.error('[PrestaKash] Mark as read failed:', err);
        });
    }

    markAsUnread(notificationId) {
        axios.post(`/notifications/${notificationId}/unread`).then(() => {
            this._loadUnreadCount();
        }).catch(err => {
            console.error('[PrestaKash] Mark as unread failed:', err);
        });
    }

    markAllAsRead() {
        axios.post('/notifications/mark-all-read').then(() => {
            const items = document.querySelectorAll('[data-notification-id]');
            items.forEach(el => {
                el.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/10');
                const dot = el.querySelector('.bg-blue-500');
                if (dot) dot.remove();
            });
            this._updateBadge(0, true);
        }).catch(err => {
            console.error('[PrestaKash] Mark all as read failed:', err);
        });
    }

    loadNotifications(page = 1) {
        return axios.get(`/notifications?page=${page}`).then(response => {
            return response.data;
        }).catch(err => {
            console.error('[PrestaKash] Load notifications failed:', err);
            return null;
        });
    }

    destroy() {
        if (this.channel && window.Echo) {
            window.Echo.leave(`notifications.${this.userId}`);
        }
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
    }
}

window.PrestaKashNotifications = PrestaKashNotifications;
