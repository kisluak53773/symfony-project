import { axiosDefault, axiosWithAuth } from "../axios";
import { type IProducerCreate, type IProducer } from "./@types";

const BASE_URL = "/producer";

export const producerService = {
  async createProducer(data: IProducerCreate) {
    const response = await axiosWithAuth.post(BASE_URL, data);

    return response.data;
  },

  async getProducersForVendor() {
    const response = await axiosWithAuth.get<IProducer[]>(`${BASE_URL}/vendor`);

    return response.data;
  },
};
