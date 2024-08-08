import { type IProduct } from "@/services/product";

export interface IFavorite {
  favoriteProducts: IProduct[];
  error: string;
}
