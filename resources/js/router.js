import { createRouter, createWebHistory } from 'vue-router';
import LoginComponent from './Auth/Login.vue';
import RegisterComponent from './Auth/Register.vue';
import DashboardComponent from './Buku/ListBuku.vue'
import ShowBuku from './Buku/ShowBuku.vue';
import AdminDashboard from './Admin/AdminDashboard.vue';
import AdminUserDashboard from './Admin/User.vue';
import AdminSettingDashboard from './Admin/Setting.vue';
import AdminEditUserDashboard from './Admin/EditUser.vue';
import AdminPinjam from './Admin/Pinjam.vue';
import AdminKembali from './Admin/Kembali.vue';
import AdminDenda from './Admin/Denda.vue';
import AdminBuku from './Admin/Buku.vue';

const routes = [
    { path: '/', component: LoginComponent },
    { path: '/register', component: RegisterComponent },
    { path: '/dashboard', component: DashboardComponent, meta: { requiresAuth: true } },
    { path: '/book/:id/show', component: ShowBuku, name: 'ShowBuku',meta: { requiresAuth: true } },

    { path: '/dashboard-admin', component: AdminDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/users', component: AdminUserDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/users/:id/edit', component: AdminEditUserDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/settings', component: AdminSettingDashboard, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/buku', component: AdminBuku, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/pinjam', component: AdminPinjam, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/kembali', component: AdminKembali, meta: { requiresAuth: true } },
    { path: '/dashboard-admin/denda', component: AdminDenda, meta: { requiresAuth: true } },
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