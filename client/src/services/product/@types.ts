import { type IProducer } from "../producer";

export interface IPaginatedProducts {
  total_items: number;
  current_page: number;
  total_pages: number;
  data: IProduct[];
}

interface IProductProducer extends Omit<IProducer, "products"> {}

export interface IProduct {
  id: number;
  title: string;
  description: string;
  compound: string;
  storageConditions: string;
  type: string;
  weight: string;
  price: number;
  image: string;
  producer: IProductProducer;
}
