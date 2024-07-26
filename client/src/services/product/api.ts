import { axiosDefault, axiosWithAuth } from "../axios";
import {
  type IPaginatedProducts,
  type IPaginatedProductsOfVendor,
  type IVendorProductUpdate,
  type IPgainatedProductVendorDoesNotSell,
  type IVendorProductCreate,
  type IGetProductsParams,
} from "./@types";
import { convertArrayToQuerryParams } from "../converter";

const BASE_URL = "/product";

export const productService = {
  async getProducts({
    page = 1,
    title = "",
    types = [],
    producers = [],
    limit = 5,
  }: IGetProductsParams) {
    let endpoint = `${BASE_URL}?page=${page}&title=${title}&limit=${limit}`;

    if (types.length > 0) {
      endpoint += convertArrayToQuerryParams("types", types);
    }

    if (producers.length > 0) {
      endpoint += convertArrayToQuerryParams("producers", producers);
    }

    const response = await axiosDefault.get<IPaginatedProducts>(endpoint);

    return response.data;
  },

  async creteProduct(data: FormData) {
    const response = await axiosWithAuth.post(`${BASE_URL}/create`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    return response.data;
  },

  async getVendorProducts(page: number = 1) {
    const respnse = await axiosWithAuth.get<IPaginatedProductsOfVendor>(
      `/vendorProduct/vendor?page=${page}`
    );

    return respnse.data;
  },

  async updateVendorProduct(data: IVendorProductUpdate, id: number) {
    const response = await axiosWithAuth.patch(
      `/vendorProduct/vendor/update/${id}`,
      data
    );

    return response.data;
  },

  async deleteProductForVendor(id: number) {
    const response = await axiosWithAuth.delete(`/vendorProduct/${id}`);

    return response.data;
  },

  async getProductsVendorDoesNotSell(page: number = 1) {
    const response =
      await axiosWithAuth.get<IPgainatedProductVendorDoesNotSell>(
        `${BASE_URL}/vendor?page=${page}`
      );

    return response.data;
  },

  async setProductFroVendor(data: IVendorProductCreate) {
    const response = await axiosWithAuth.post("/vendorProduct", data);

    return response.data;
  },
};
