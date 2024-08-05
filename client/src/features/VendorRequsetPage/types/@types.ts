import { type IOrder, type IOrderProduct } from "@/services/order";

export interface IVendorRequestProps {
  orderId: number;
}

export interface IRequestProductListProps {
  products: IOrderProduct[];
}

export interface IRequestProductItemProps {
  product: IOrderProduct;
}

export interface IOrderDetailsProps {
  order: IOrder;
}

export interface IRequestDetailsProps {
  orderId: number;
}
