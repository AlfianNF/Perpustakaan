import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from './Auth/Login.vue';
import RegisterComponent from './Auth/Register.vue';

const routes = [
    { path: '/', component: LoginComponent },
    { path: '/register', component: RegisterComponent },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;