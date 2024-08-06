import { type IOrder, type IOrderProduct } from "@/services/order";

export interface IVendorRequestProps {
  orderId: number;
}

export interface IRequestProductListProps {
  products: IOrderProduct[];
  order: IOrder;
  handleOrderUpdate: (order: IOrder) => void;
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

export interface IOrderModalFormProps {
  order: IOrder;
  setModalActive: React.Dispatch<React.SetStateAction<boolean>>;
  handleOrderUpdate: (order: IOrder) => void;
}

export interface IOrderEditFields {
  deliveryTime: string;
}

export type TOrderEdit = "deliveryTime";
