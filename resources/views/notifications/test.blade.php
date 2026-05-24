<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notification Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Notification System Test</h1>

        @auth
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <p class="mb-2">Logged in as: <strong>{{ Auth::user()->name }}</strong></p>
                <p>User ID: {{ Auth::user()->id }}</p>
            </div>

            <div x-data="{
                notifications: [],
                unreadCount: 0,
                loading: false,
                error: null,
                async loadNotifications() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const response = await fetch('/notifications');
                        if (!response.ok) throw new Error('Failed to fetch');
                        const data = await response.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    } catch (err) {
                        this.error = err.message;
                        console.error('Error loading notifications:', err);
                    } finally {
                        this.loading = false;
                    }
                },
                async markAsRead(id) {
                    try {
                        const response = await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        if (!response.ok) throw new Error('Failed to mark as read');
                        await this.loadNotifications();
                    } catch (err) {
                        console.error('Error marking as read:', err);
                    }
                },
                async markAllAsRead() {
                    try {
                        const response = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        if (!response.ok) throw new Error('Failed to mark all as read');
                        await this.loadNotifications();
                    } catch (err) {
                        console.error('Error marking all as read:', err);
                    }
                }
            }"
            x-init="loadNotifications()"
            class="bg-white rounded-lg shadow p-6">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Notifications</h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            Unread: <span class="font-bold" x-text="unreadCount"></span>
                        </span>
                        <button @click="loadNotifications()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Refresh
                        </button>
                        <button x-show="unreadCount > 0" @click="markAllAsRead()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Mark All Read
                        </button>
                    </div>
                </div>

                <div x-show="loading" class="text-center py-4">
                    <span class="text-gray-500">Loading...</span>
                </div>

                <div x-show="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <span x-text="error"></span>
                </div>

                <div x-show="!loading && !error">
                    <template x-if="notifications.length === 0">
                        <div class="text-center py-8 text-gray-500">
                            No notifications found
                        </div>
                    </template>

                    <div class="space-y-2">
                        <template x-for="notification in notifications" :key="notification.id">
                            <div @click="!notification.is_read && markAsRead(notification.id)"
                                 :class="notification.is_read ? 'bg-white' : 'bg-blue-50'"
                                 class="p-4 border rounded hover:bg-gray-50 cursor-pointer transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold" x-text="notification.title"></h3>
                                        <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                        <p class="text-xs text-gray-400 mt-2" x-text="notification.created_at"></p>
                                    </div>
                                    <template x-if="!notification.is_read">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Raw Database Data</h3>
                @php
                    $userNotifications = Auth::user()->notifications()->latest()->limit(5)->get();
                @endphp
                <pre class="bg-gray-100 p-4 rounded text-xs overflow-x-auto">{{ json_encode($userNotifications->toArray(), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @else
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                You are not logged in. Please <a href="/login" class="underline">login</a> to see notifications.
            </div>
        @endauth
    </div>
</body>
</html>