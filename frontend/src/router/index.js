import { createRouter, createWebHistory } from "vue-router";
import BaseLayout from "@/layouts/BaseLayout.vue";

// Páginas
import Login from "@/pages/Auth/Login.vue";
import Register from "@/pages/Auth/Register.vue";
import Dashboard from "@/pages/Dashboard.vue";
import Meetings from "@/pages/Meetings.vue";
import Clients from "@/pages/Clients.vue";

const routes = [
    { path: "/login", component: Login },
    { path: "/register", component: Register },
    {
        path: "/",
        component: BaseLayout,
        children: [
            { path: "dashboard", component: Dashboard },
            { path: "meetings", component: Meetings },
            { path: "clients", component: Clients },
        ],
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");
    if (to.meta.requiresAuth && !token) {
        next("/login");
    } else {
        next();
    }
});

export default router;
