import '@/assets/css/main.css';
import 'element-plus/dist/index.css';

import { createApp } from 'vue';
import App from '@/App.vue';

import router from '@/router';
import { createPinia } from 'pinia';

import ElementPlus from 'element-plus';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';

const app = createApp(App);
const pinia = createPinia();

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

app
    .use(pinia)
    .use(router)
    .use(ElementPlus)
    .mount('#app');
