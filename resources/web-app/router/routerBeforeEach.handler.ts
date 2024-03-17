import { Router } from 'vue-router';
import { getLoggedInUserApi } from '@/datasources/api/auth/getLoggedInUser.api';
import { useUserStore } from '@/stores/user.store';

const baseTitle = 'Laravel Caro';

export const registerRouterBeforeEach = (router: Router) =>
  router.beforeEach(async (to, from, next) => {
    if (to.meta.title) {
      document.title = `${to.meta.title} | ${baseTitle}`;
    } else {
      document.title = baseTitle;
    }

    const user = await getLoggedInUserApi();

    if (!user) {
      if (!to.meta.requiresAuth) {
        return next();
      }

      return next({ name: 'login' });
    }

    const userStore = useUserStore();
    userStore.setUser(user);

    if (!to.meta.requiresAuth) {
      return next({ name: 'rooms' });
    }

    next();
  });
