import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { registerRouterBeforeEach } from '@/router/routerBeforeEach.handler';

declare module 'vue-router' {
  // eslint-disable-next-line @typescript-eslint/consistent-type-definitions
  interface RouteMeta {
    // Page title
    title?: string;
    // Require auth or not
    requiresAuth?: boolean;
  }
}

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/screens/Auth/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'login',
        component: () => import('@/screens/Auth/LoginScreen.vue'),
        meta: {
          title: 'Đăng nhập',
        },
      },
      {
        path: 'login-with-google',
        name: 'login-with-google',
        component: () => import('@/screens/Auth/GoogleLoginScreen.vue'),
        meta: {
          title: 'Đăng nhập với Google',
        },
      },
    ],
  },
  {
    path: '/game',
    component: () => import('@/screens/Game/GameLayout.vue'),
    children: [],
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('@/screens/common/Error404Page.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ left: 0, top: 0 }),
});

registerRouterBeforeEach(router);

export default router;