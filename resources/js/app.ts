import '../css/app.css';
import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';

// Create the Vue application
const app = createApp(App);

// Use Pinia for state management
app.use(createPinia());

// Mount the app to the #app element
app.mount('#app');
