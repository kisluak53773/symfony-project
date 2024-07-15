import { AxiosError } from "axios";

export const errorCatch = (error: any): string => {
  const message = error?.response?.data?.message;

  return message || error.message;
};

export const getErrorStatusCode = (error: any): number | null => {
  if (error instanceof AxiosError) {
    return error.response?.status as number;
  }

  return null;
};
