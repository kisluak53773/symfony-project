import { type IRegisterData, type ILoginData, type ITokens } from "./@types";
import { axiosDefault, axiosWithRefresh } from "../axios";
import { setTokens } from "../cookie";
import { removeTokens } from "../cookie";

const BASE_URL = "/auth";

export const authService = {
  async login(data: ILoginData) {
    const response = await axiosDefault.post<ITokens>(
      `${BASE_URL}/token/login`,
      data
    );

    if (response.data.token && response.data.refresh_token) {
      setTokens(response.data.token, response.data.refresh_token);
    }

    return response;
  },

  async register(data: IRegisterData) {
    const response = await axiosDefault.post(`${BASE_URL}/register`, data);

    if (response.status === 201) {
      await authService.login({ password: data.password, phone: data.phone });
    }

    return response;
  },

  async refresh() {
    const response = await axiosWithRefresh.get<ITokens>(
      `${BASE_URL}/token/refresh`
    );

    if (response.data.token && response.data.refresh_token) {
      setTokens(response.data.token, response.data.refresh_token);
    }

    return response;
  },

  async logout() {
    const response = await axiosWithRefresh.get(`${BASE_URL}/token/logout`);

    if (response.status === 200) {
      removeTokens();
    }
  },
};
