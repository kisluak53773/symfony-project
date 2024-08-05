import { type ICartItem } from "@/services/cart";

export type TOrderNames = "deliveryTime" | "paymentMethod" | "comment";

export interface ICartProductItemProps {
  product: ICartItem;
}
