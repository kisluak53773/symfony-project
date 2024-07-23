import { axiosWithAuth } from "../axios";
import { type IUser, type IProfileUpdate } from "./@types";

const BASE_URL = "/user";

export const userService = {
  async getCurrentUser() {
    const response = await axiosWithAuth.get<IUser>(`${BASE_URL}/current`);

    return response.data;
  },

  async updateProfile(data: IProfileUpdate) {
    const response = await axiosWithAuth.patch(BASE_URL, data);

    return response.data;
  },
};
