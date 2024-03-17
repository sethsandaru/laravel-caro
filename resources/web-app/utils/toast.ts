import { notify } from '@kyvg/vue3-notification';
import { ApiErrorException } from '@/datasources/api/axios';

export const showInfoAlert = (text: string, title?: string) =>
  notify({
    type: 'info',
    text,
    title,
  });

export const showSuccessAlert = (text: string, title?: string) =>
  notify({
    type: 'success',
    text,
    title,
  });

export const showWarningAlert = (text: string, title?: string) =>
  notify({
    type: 'warn',
    text,
    title,
  });

export const showErrorAlert = (text: string, title?: string) =>
  notify({
    type: 'error',
    text,
    title,
  });

export const showValidationError = () =>
  showErrorAlert(
    'Xin hãy kiểm tra lại các trường bị lỗi, sửa lỗi và thử lại.',
    'Lỗi thông tin'
  );

export const showUnexpectedError = () =>
  showErrorAlert('Đã có lỗi không xác định xảy ra, xin hãy thử lại.', 'Lỗi');

export const showErrorFromException = (err: ApiErrorException): null => {
  showErrorAlert(err.message, 'Đã xảy ra lỗi');
  return null;
};
