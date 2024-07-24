import { axiosWithAuth } from "../axios";
import { type IVendor, type IVendorToUpdate } from "./@types";

const BASE_URL = "/vendor";

export const vendorService = {
  async getCurrentVendor() {
    const response = await axiosWithAuth.get<IVendor>(`${BASE_URL}/current`);

    return response.data;
  },

  async updateCurrentVendor(data: IVendorToUpdate) {
    const response = await axiosWithAuth.patch(`${BASE_URL}/current`, data);

    return response.data;
  },
};
