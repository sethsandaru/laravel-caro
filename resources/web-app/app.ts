import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import '@/css/app.css';
import router from '@/routes';
import notifications from '@kyvg/vue3-notification';

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);
app.use(notifications);

app.mount('#app');
