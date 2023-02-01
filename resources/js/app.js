import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';

import ExampleComponent from '@/js/components/ExampleComponent.vue';

const app = createApp(ExampleComponent);

app
    .use(ElementPlus)
    .mount('#app');
