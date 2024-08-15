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
  setIsModalActive: React.Dispatch<React.SetStateAction<boolean>>;
}

export interface ISearchItemProps {
  product: IProduct;
}

export interface IHeaderSearchModalProps {
  setIsModalActive: React.Dispatch<React.SetStateAction<boolean>>;
}

interface ILinks {
  id: number;
  href: string;
  title: string;
  img: string;
}

export interface IHeaderLinksProps {
  links: ILinks[];
}
