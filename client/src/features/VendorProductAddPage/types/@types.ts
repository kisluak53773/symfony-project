import { type IProductVendorDoesNotSell } from "@/services/product";

export interface IExistingProductItemProps {
  product: IProductVendorDoesNotSell;
  handleRefetch: () => void;
}

export interface IExistingProductModalProps {
  productId: number;
  setIsModalActive: React.Dispatch<React.SetStateAction<boolean>>;
  handleRefetch: () => void;
}
