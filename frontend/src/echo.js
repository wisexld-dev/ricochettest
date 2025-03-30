import Echo from 'laravel-echo';
import Pusher from "pusher-js";

export function initEcho() {
    try {
        window.echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            encrypted: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: 'http://localhost:8000/api/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            }
        });

        return window.echo;
    } catch (error) {
        console.error('Failed to initialize Echo:', error);
        return null;
    }
}