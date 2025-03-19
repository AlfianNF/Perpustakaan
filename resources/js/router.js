import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from './Auth/Login.vue';
import RegisterComponent from './Auth/Register.vue';
import DashboardComponent from './Buku/ListBuku.vue'
import ShowBuku from './Buku/ShowBuku.vue';
import AdminDashboard from './Admin/AdminDashboard.vue';
import AdminUserDashboard from './Admin/User.vue';
import AdminSettingDashboard from './Admin/Setting.vue';
import AdminEditUserDashboard from './Admin/EditUser.vue'

const routes = [
    { path: '/', component: LoginComponent },
    { path: '/register', component: RegisterComponent },
    { path: '/dashboard', component: DashboardComponent, meta: { requiresAuth: true } },
    { path: '/book/:id/show', component: ShowBuku, name: 'ShowBuku' },

    { path: '/dashboard-admin', component: AdminDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/users', component: AdminUserDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/users/:id/edit', component: AdminEditUserDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/settings', component: AdminSettingDashboard, meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!localStorage.getItem('token')) {
            next('/');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;