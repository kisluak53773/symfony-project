import { axiosDefault, axiosWithAuth } from "../axios";
import { type IPaginatedProducts } from "./@types";

const BASE_URL = "/product";

export const productService = {
  async getProducts(page: number = 1) {
    const response = await axiosDefault.get<IPaginatedProducts[]>(
      `${BASE_URL}?page=${page}`
    );

    return response.data;
  },

  async creteProduct(data: FormData) {
    const response = await axiosWithAuth.post(`${BASE_URL}/create`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  },
};
