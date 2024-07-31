import { axiosWithAuth } from "../axios";
import {
  type ICartItem,
  type ICartUpdateData,
  type IIncreaseResponse,
} from "./@types";
import { type IDefaultResponse } from "@/types";

const BASE_URL = "/cart";

export const cartService = {
  async getCart() {
    const response = await axiosWithAuth.get<ICartItem[]>(
      `${BASE_URL}/prodcuts`
    );

    return response.data;
  },

  async addProductToCart(data: ICartUpdateData) {
    const response = await axiosWithAuth.post<IDefaultResponse>(
      `${BASE_URL}/add`,
      data
    );

    return response.data;
  },

  async incrementProductQuantity(data: ICartUpdateData) {
    const response = await axiosWithAuth.post<IIncreaseResponse>(
      `${BASE_URL}/increase`,
      data
    );

    return response.data;
  },

  async decrementProductQuantity(data: ICartUpdateData) {
    const response = await axiosWithAuth.post(`${BASE_URL}/decrease`, data);

    return response.data;
  },

  async deleteProductFromCart(vendorId: number) {
    const response = await axiosWithAuth.delete(
      `${BASE_URL}/remove/${vendorId}`
    );

    return response.data;
  },

  async deleteAllProdcutsFromCart() {
    const response = await axiosWithAuth.delete(`${BASE_URL}/removeAll`);

    return response.data;
  },
};
