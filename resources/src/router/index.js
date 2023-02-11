import Login from '@/pages/AuthLogin.vue';
import Register from '@/pages/AuthRegister.vue';
import Reset from '@/pages/AuthReset.vue';
import Main from '@/pages/Main.vue';
import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from '@/store/user.store';

function checkUserAuthentication (to, from, next) {
    const { authenticated } = useUserStore();

    if (!authenticated) {
        next({ name: 'login' });
    } else {
        next();
    }
}


const routes = [{
    path: '/',
    name: 'main',
    component: Main,
    beforeEnter: [checkUserAuthentication],
}, {
    path: '/login',
    name: 'login',
    component: Login,
}, {
    path: '/register',
    name: 'register',
    component: Register,
}, {
    path: '/reset',
    name: 'reset',
    component: Reset,
}];

const router = createRouter({
    routes,
    history: createWebHistory(),
});

export default router;
