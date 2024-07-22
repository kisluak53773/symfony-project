import { IOption } from "@/types";
import { Control } from "react-hook-form";

export type TProductNames =
  | "title"
  | "weight"
  | "description"
  | "compound"
  | "storageConditions";

export type TProductVendorNames =
  | "title"
  | "weight"
  | "description"
  | "compound"
  | "storageConditions"
  | "price"
  | "quantity";

export interface IProductCreate {
  title: string;
  weight: string;
  description: string;
  compound: string;
  storageConditions: string;
  typeId: string;
  producerId: string;
}

export interface IProductVendorCreate extends IProductCreate {
  price: string;
  quantity: string;
}

export interface ICustomSelectProps {
  control: Control<IProductVendorCreate, any>;
  options: IOption[];
  name: "typeId" | "producerId";
  placeholder: string;
  requiredMessage: string;
}
