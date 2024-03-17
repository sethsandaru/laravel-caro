import axios, { AxiosError, AxiosResponse } from 'axios';

const options = {
  baseURL: import.meta.env.VITE_SERVER_API_BASE_URL,
  timeout: 60_000,
};

export const httpClient = axios.create({
  ...options,
  withCredentials: true,
});

export type GenericApiError = AxiosError<Record<string, unknown>>;

export class ApiErrorException extends Error {}

export const assertOutcomeSuccess = (
  res: AxiosResponse<{ outcome: string } | undefined | null>
): boolean => res.data?.outcome === 'SUCCESS';
