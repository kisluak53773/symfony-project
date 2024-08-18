import { type IPagination } from "@/types";
import { type IProducer } from "../producer";
import { type IType } from "../type";
import { type ISort } from "@/features/ProductsPage/types";

export interface IPaginatedProducts extends IPagination {
  data: IProduct[];
}

interface IListProductVendorProduct {
  id: number;
  price: string;
  quantity: number;
  vendorId: number;
}

export interface IProduct {
  id: number;
  title: string;
  description: string;
  compound: string;
  storageConditions: string;
  weight: string;
  image: string;
  vendorProducts: IListProductVendorProduct[];
  typeId: number;
  producerId: number;
  averageRating: number;
}

export interface IProductOfVendor {
  id: number;
  price: string;
  quantity: number;
  product: Omit<IProduct, "vendorProducts">;
}

export interface IPaginatedProductsOfVendor extends IPagination {
  data: IProductOfVendor[];
}

export interface IVendorProductUpdate {
  quantity: number;
  price: string;
}

export interface IProductVendorDoesNotSell
  extends Omit<IProduct, "typeId" | "producerId" | "vendorProducts"> {
  producer: IProducer;
  type: IType;
}

export interface IPgainatedProductVendorDoesNotSell extends IPagination {
  data: IProductVendorDoesNotSell[];
}

export interface IVendorProductCreate extends IVendorProductUpdate {
  productId: number;
}

export interface IGetProductsParams {
  page?: number;
  title?: string;
  types?: number[];
  producers?: number[];
  limit?: number;
  sort?: ISort;
}

export interface IVendorProduct {
  id: number;
  price: string;
  quantity: number;
  vendorId: number;
  vendorTitle: string;
}

export interface ISpecificPoduct extends Omit<IProduct, "vendorProducts"> {
  producer: IProducer;
  vendorProducts: IVendorProduct[];
  type: IType;
}
