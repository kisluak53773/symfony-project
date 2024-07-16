import { type IProduct } from "@/services/product";

export interface ICartProduct extends IProduct {
  quantity: number;
}

export interface ICart {
  producst: ICartProduct[];
  error: string;
}
