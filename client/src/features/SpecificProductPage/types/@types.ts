import { type ISpecificPoduct, type IVendorProduct } from "@/services/product";

export interface ISpecificProductPageProps {
  productId: number;
}

export interface IReviewListProps {
  productId: number;
}

export interface IProductDataProps {
  productId: number;
}

export interface IProductDescriptionProsp {
  product: ISpecificPoduct;
  setProduct: React.Dispatch<React.SetStateAction<ISpecificPoduct | null>>;
}

export interface IVendorListProps {
  product: ISpecificPoduct;
  setProduct: React.Dispatch<React.SetStateAction<ISpecificPoduct | null>>;
}

export interface IVendorItemProsp {
  vendor: IVendorProduct;
  product: ISpecificPoduct;
  setProduct: React.Dispatch<React.SetStateAction<ISpecificPoduct | null>>;
}

export interface IReviewsProps {
  productId: number;
}
