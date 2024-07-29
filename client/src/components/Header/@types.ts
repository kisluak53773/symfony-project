import { type IProduct } from "@/services/product";

export interface IHeaderImageProps {
  type: string;
}

export interface IHeaderLinkProps {
  href: string;
  title: string;
  img: string;
}

export interface ISearchListProsp {
  search: string;
}

export interface ISearchItemProps {
  product: IProduct;
}
