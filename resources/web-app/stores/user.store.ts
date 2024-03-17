import { defineStore } from 'pinia';
import { computed, Ref, ref } from 'vue';
import { LoggedInUser } from '@/datasources/api/auth/getLoggedInUser.api';

export const useUserStore = defineStore('loggedInUser', () => {
  const user = ref<LoggedInUser>() as Ref<LoggedInUser>;

  const setUser = (wantedUser: LoggedInUser) => {
    user.value = { ...wantedUser };
  };

  const fullName = computed(() => user.value?.name);

  const profilePictureUrl = computed(() => user.value?.profilePicture);

  return {
    user,
    setUser,
    fullName,
    profilePictureUrl,
  };
});
