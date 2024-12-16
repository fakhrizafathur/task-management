import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

const app = createApp(App);

app.use(router); // Use router
app.mount('#app'); // Mount the app to `#app` in `index.html`
