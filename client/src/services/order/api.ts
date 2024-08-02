import { axiosWithAuth } from "../axios";
import {
  type IOrderCreate,
  type IPaginatedOrders,
  type IOrdersPaginationRequestData,
} from "./@types";

const BASE_URL = "/order";

export const orderService = {
  async createOrder(data: IOrderCreate) {
    const response = await axiosWithAuth.post(BASE_URL, data);

    return response;
  },

  async getOrdersOfCurrentUser({
    page,
    limit = 5,
  }: IOrdersPaginationRequestData) {
    const response = await axiosWithAuth.get<IPaginatedOrders>(
      `${BASE_URL}/current?page=${page}&limit=${limit}`
    );

    return response.data;
  },

  async cancelOrder(orderId: number) {
    const response = await axiosWithAuth.patch(
      `${BASE_URL}/customer/${orderId}`
    );

    return response.data;
  },
};
