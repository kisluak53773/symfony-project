import { type ICartItem } from "@/services/cart";

export interface ICart {
  producst: ICartItem[];
  error: string;
}

export interface ICartItemCreate extends Omit<ICartItem, "id"> {}
