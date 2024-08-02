import { type IOrder } from "@/services/order";

export interface IOrderItemProps {
  order: IOrder;
  handleOrderUpdate: (order: IOrder) => void;
}

export interface IOrderItemSectionProps {
  heading: string;
  data: string;
}
