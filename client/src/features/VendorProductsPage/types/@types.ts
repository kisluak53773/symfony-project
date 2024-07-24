import {
  type IProductOfVendor,
  type IVendorProductUpdate,
} from "@/services/product";

export interface IProductVendorItemProps {
  vendorProduct: IProductOfVendor;
  handleRefetch: () => void;
  handleProductOfVendorUpdate: (data: IVendorProductForm, id: number) => void;
}

export interface IVerdorProductModalProps {
  vendorProduct: IProductOfVendor;
  setIsModelActive: React.Dispatch<React.SetStateAction<boolean>>;
  handleProductOfVendorUpdate: (data: IVendorProductForm, id: number) => void;
}

export type TVendorProductNames = "price" | "quantity";

export interface IVendorProductForm
  extends Omit<IVendorProductUpdate, "vendorProductId"> {}
