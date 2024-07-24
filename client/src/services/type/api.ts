import { axiosWithAuth } from "../axios";
import { type IType } from "./@types";

const BASE_URL = "/type";

export const typeService = {
  async createaType(data: FormData) {
    const response = await axiosWithAuth.post(BASE_URL, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  },

  async getTypesForVendor() {
    const response = await axiosWithAuth.get<IType[]>(`${BASE_URL}/vendor`);

    return response.data;
  },
};
