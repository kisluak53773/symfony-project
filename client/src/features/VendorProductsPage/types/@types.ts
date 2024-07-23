import {
  type IProductOfVendor,
  type IVendorProductUpdate,
} from "@/services/product";

export interface IProductVendorItemProps {
  vendorProduct: IProductOfVendor;
}

export interface IVerdorProductModalProps {
  vendorProduct: IProductOfVendor;
  setIsModelActive: React.Dispatch<React.SetStateAction<boolean>>;
}

export type TVendorProductNames = "price" | "quantity";

export interface IVendorProductForm
  extends Omit<IVendorProductUpdate, "vendorProductId"> {}
