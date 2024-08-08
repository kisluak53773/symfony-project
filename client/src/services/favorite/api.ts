import { axiosWithAuth } from "../axios";
import { type IProduct } from "../product";

const BASE_URL = "/favorite";

export const favoriteService = {
  async addToFavorite(productId: number) {
    const response = await axiosWithAuth.post(`${BASE_URL}/${productId}`);

    return response.data;
  },

  async deleteFromFavorite(productId: number) {
    const response = await axiosWithAuth.delete(`${BASE_URL}/${productId}`);

    return response.data;
  },

  async getAllFavorite() {
    const response = await axiosWithAuth.get<IProduct[]>(`${BASE_URL}`);

    return response.data;
  },
};
