export interface IPaginatedProducts {
  total_items: number;
  current_page: number;
  total_pages: number;
  data: IProduct[];
}

interface IListProductVendorProduct {
  price: string;
  quantity: number;
  vendorId: number;
}

export interface IProduct {
  id: number;
  title: string;
  description: string;
  compound: string;
  storageConditions: string;
  weight: string;
  image: string;
  vendorProducts: IListProductVendorProduct[];
  typeId: number;
  producerId: number;
}
