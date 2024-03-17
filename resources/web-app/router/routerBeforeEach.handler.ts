import { Router } from 'vue-router';

const baseTitle = 'Laravel Caro';

export const registerRouterBeforeEach = (router: Router) =>
  router.beforeEach(async (to, from, next) => {
    if (to.meta.title) {
      document.title = `${to.meta.title} | ${baseTitle}`;
    } else {
      document.title = baseTitle;
    }

    // const user = await getLoggedInUser();
    const user = undefined;
    if (!user) {
      if (!to.meta.requiresAuth) {
        return next();
      } else {
        return next({ name: 'login' });
      }
    }

    // const userStore = useUserStore();
    // userStore.setUser(user);

    if (!to.meta.requiresAuth) {
      return next({ name: 'game-rooms' });
    }

    next();
  });
