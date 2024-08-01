import { axiosWithAuth } from "../axios";
import { type IOrderCreate } from "./@types";

const BASE_URL = "/order";

export const orderService = {
  async createOrder(data: IOrderCreate) {
    const response = await axiosWithAuth.post(BASE_URL, data);

    return response;
  },

  async getOrdersOfCurrentUser() {
    const response = await axiosWithAuth.get(`${BASE_URL}/current`);

    return response.data;
  },
};
