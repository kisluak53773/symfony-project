import { axiosWithAuth } from "../axios";
import { IUser } from "./@types";

const BASE_URL = "/user";

export const userService = {
  async getCurrentUser() {
    const response = await axiosWithAuth.get<IUser>(`${BASE_URL}/current`);

    return response.data;
  },
};
