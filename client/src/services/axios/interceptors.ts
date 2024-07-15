import axios, { type CreateAxiosDefaults } from "axios";
import { getAccessToken, removeTokens } from "../cookie";
import { errorCatch, getErrorStatusCode } from "./error";
import { authService } from "../auth";

const options: CreateAxiosDefaults = {
  baseURL: "http://127.0.0.1:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
};

export const axiosDefault = axios.create(options);
export const axiosWithAuth = axios.create(options);

axiosWithAuth.interceptors.request.use((config) => {
  const accessToken = getAccessToken();

  if (config?.headers && accessToken) {
    config.headers.Authorization = `Bearer ${accessToken}`;
  }

  return config;
});

axiosWithAuth.interceptors.response.use(
  (config) => config,
  async (error) => {
    const originalRequest = error.config;
    if (
      getErrorStatusCode(error) === 401 &&
      error.config &&
      !error.config.isRetry
    ) {
      originalRequest.isRetry = true;
      try {
        await authService.refresh();
        return axiosWithAuth.request(originalRequest);
      } catch (error) {
        if (getErrorStatusCode(error) === 401) removeTokens();
      }
    }
  }
);
