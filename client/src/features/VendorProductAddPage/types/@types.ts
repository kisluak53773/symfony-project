import { type IProductVendorDoesNotSell } from "@/services/product";

export interface IExistingProductItemProps {
  product: IProductVendorDoesNotSell;
}

export interface IExistingProductModalProps {
  productId: number;
  setIsModalActive: React.Dispatch<React.SetStateAction<boolean>>;
}
