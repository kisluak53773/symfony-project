import { type IPagination } from "@/types";

export interface IOrderCreate {
  deliveryTime: string;
  paymentMethod: string;
  comment?: string;
}

export interface IOrder {
  id: number;
  paymentMethod: string;
  deliveryTime: string;
  comment: string;
  orderStatus: string;
  createdAt: string;
  updatedAt: string;
  totalPrice: number;
  deliveryAddress: string;
}

export interface IPaginatedOrders extends IPagination {
  data: IOrder[];
}
