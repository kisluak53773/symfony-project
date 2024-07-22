import { type IProduct } from "@/services/product";
import { ROLES } from "@/constants";

export interface IProductItemProps {
  product: IProduct;
}

export interface IJwtPayload {
  iat: number;
  exp: number;
  roles: ROLES;
  username: string;
}

export interface IOption {
  value: string;
  label: string;
}
