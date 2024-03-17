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
        component: () => import('@/screens/Auth/Login/LoginScreen.vue'),
        meta: {
          title: 'Đăng nhập',
        },
      },
      {
        path: 'login-with-google',
        name: 'login-with-google',
        component: () =>
          import('@/screens/Auth/LoginWithGoogle/LoginWithGoogleScreen.vue'),
        meta: {
          title: 'Đăng nhập với Google',
        },
      },
    ],
  },
  {
    path: '/game',
    component: () => import('@/screens/Game/GameLayout.vue'),
    children: [
      {
        path: '/',
        name: 'rooms',
        component: () => import('@/screens/Game/Rooms/RoomsScreen.vue'),
        meta: {
          title: 'Các phòng game',
          requiresAuth: true,
        },
      },
      {
        path: '/room/:id',
        name: 'room',
        component: () => import('@/screens/Game/Room/RoomScreen.vue'),
        meta: {
          title: 'Phòng game',
          requiresAuth: true,
        },
      },
    ],
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('@/screens/Common/Error404Page.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ left: 0, top: 0 }),
});

registerRouterBeforeEach(router);

export default router;
