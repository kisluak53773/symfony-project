import { type IProduct } from "../product";

interface IPrducerProduct extends Omit<IProduct, "producer"> {}

export interface IProducer {
  id: number;
  title: string;
  country: string;
  address: string;
  products: IProduct[];
}
