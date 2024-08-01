import { type IDefaultResponse } from "@/types";

export interface ICartItem {
  id: number;
  quantity: number;
  vendorProductId: number;
  price: string;
  productId: number;
  productImage: string;
  productWeight: string;
  productTitle: string;
  inStock: number;
}

export interface ICartUpdateData {
  quantity: number;
  vendorProductId: number;
}

export interface IIncreaseResponse extends Omit<IDefaultResponse, "id"> {
  quantity: number;
}
