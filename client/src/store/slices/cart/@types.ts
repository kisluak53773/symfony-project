import { type IProduct } from "@/services/product";

export interface ICartProduct extends Omit<IProduct, "vendorProducts"> {
  quantity: number;
  price: string;
  vendorId: number;
}

export interface ICart {
  producst: ICartProduct[];
  error: string;
}
