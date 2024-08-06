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

export interface IOrderProduct {
  id: number;
  quantity: number;
  vendorProductId: number;
  price: string;
  productId: number;
  productImage: string;
  productWeight: string;
  productTitle: string;
}

export interface IPaginatedOrders extends IPagination {
  data: IOrder[];
}

export interface IOrdersPaginationRequestData {
  page: number;
  limit?: number;
}

export interface ISpecificRequestData {
  orderData: IOrder;
  products: IOrderProduct[];
}
