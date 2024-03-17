import { ref } from 'vue';

export const useLoading = () => {
  const isLoading = ref(false);

  const startLoading = () => {
    isLoading.value = true;
  };

  const stopLoading = () => {
    isLoading.value = false;
  };

  const withLoading = (handler: () => Promise<void>) => async () => {
    if (isLoading.value) {
      return;
    }

    startLoading();

    await handler().catch((err) => console.error(err));

    stopLoading();
  };

  return {
    isLoading,
    startLoading,
    stopLoading,
    withLoading,
  };
};
