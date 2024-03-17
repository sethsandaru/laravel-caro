import { httpClient } from '@/datasources/api/axios';
import { AxiosResponse } from 'axios';

export type LoggedInUser = {
  ulid: string;
  email: string;
  name: string;
  profilePicture: string;
};

export const getLoggedInUserApi = (): Promise<LoggedInUser | undefined> =>
  httpClient
    .get('auth/logged-in-user')
    .then((res: AxiosResponse<LoggedInUser>) => res.data)
    .catch(() => undefined);
