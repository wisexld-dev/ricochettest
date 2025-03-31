import { createRouter, createWebHistory } from "vue-router";
import BaseLayout from "@/layouts/BaseLayout.vue";
import Login from "@/pages/Auth/Login.vue";
import Register from "@/pages/Auth/Register.vue";
import Dashboard from "@/pages/Dashboard.vue";
import Meetings from "@/pages/Meetings.vue";
import Clients from "@/pages/Clients.vue";

const routes = [
    {
        path: '/',
        name: 'Home',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
    },
    {
        path: "/dashboard",
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: "/clients",
        component: Clients,
        meta: { requiresAuth: true }
    },
    {
        path: "/schedule",
        component: Meetings,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Protege rotas autenticadas
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("token");

    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
    } else {
        next();
    }
});

export default router;
