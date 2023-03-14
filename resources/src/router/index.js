import Login from '@/pages/AuthLogin.vue';
import Register from '@/pages/AuthRegister.vue';
import Reset from '@/pages/AuthReset.vue';
import Main from '@/pages/Main.vue';
import PublicFile from '@/pages/PublicFile.vue';
import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from '@/store/user.store';

function onlyForAuthenticated(to, from, next) {
    const { authenticated } = useUserStore();

    if (!authenticated) {
        next({ name: 'login' });
    } else {
        next();
    }
}

function onlyForUnauthenticated(to, from, next) {
    const { authenticated } = useUserStore();
    if (authenticated) {
        next({ name: 'main' });
    } else {
        next();
    }
}


const routes = [{
    path: '/',
    name: 'main',
    component: Main,
    beforeEnter: [onlyForAuthenticated],
}, {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter: [onlyForUnauthenticated],
}, {
    path: '/register',
    name: 'register',
    component: Register,
    beforeEnter: [onlyForUnauthenticated],
}, {
    path: '/reset',
    name: 'reset',
    component: Reset,
    beforeEnter: [onlyForUnauthenticated],
}, {
    path: '/public/:hash',
    name: 'public',
    component: PublicFile,
}];

const router = createRouter({
    routes,
    history: createWebHistory(),
});

export default router;
