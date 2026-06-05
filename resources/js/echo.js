import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const pusherKey = document.querySelector('meta[name="pusher-app-key"]')?.content || import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = document.querySelector('meta[name="pusher-cluster"]')?.content || import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap1';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: pusherKey,
    cluster: pusherCluster,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
});
