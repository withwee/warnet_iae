import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Mount Vue app for chat component (only on chat page)
document.addEventListener('DOMContentLoaded', () => {
    const chatAppElement = document.getElementById('chat-app');

    if (chatAppElement) {
        // Dynamically import Vue and component only when needed
        import('vue').then(({ createApp }) => {
            import('./components/GroupChat.vue').then((module) => {
                const app = createApp({});
                app.component('group-chat', module.default);
                app.mount('#chat-app');
                console.log('Vue chat app mounted successfully!');
            });
        });
    }
});
