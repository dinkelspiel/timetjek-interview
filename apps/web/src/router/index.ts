import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      redirect: "/auth/login",
    },
    {
      path: "/about",
      name: "about",
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import("../pages/About.vue"),
    },
    {
      path: "/overview",
      name: "overview",
      component: () => import("../pages/Overview.vue"),
    },
    {
      path: "/settings",
      name: "settings",
      component: () => import("../pages/Settings.vue"),
    },
    {
      path: "/auth/login",
      name: "login",
      component: () => import("../pages/auth/Login.vue"),
    },
    {
      path: "/auth/signup",
      name: "signup",
      component: () => import("../pages/auth/Signup.vue"),
    },
  ],
});

export default router;
