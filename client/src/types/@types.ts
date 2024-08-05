import { type IProduct } from "@/services/product";
import { ROLES } from "@/constants";

export interface IProductItemProps {
  product: IProduct;
}

export interface IJwtPayload {
  iat: number;
  exp: number;
  roles: ROLES[];
  username: string;
}

export interface IOption {
  value: string;
  label: string;
}

export interface IPagination {
  total_items: number;
  current_page: number;
  total_pages: number;
}

export interface IPaginationHook {
  totalPageCount: number;
  siblingCount?: number;
  currentPage: number;
}

export interface IDefaultResponse {
  message: string;
  id: number;
}

export interface IDeliveryStateProps {
  status: string;
}
