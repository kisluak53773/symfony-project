import { axiosWithAuth } from "../axios";
import { type IReviewCreate, type IReview, type IReviewPatch } from "./@types";

const BASE_URL = "/review";

export const reviewService = {
  async createReview(review: IReviewCreate) {
    const response = await axiosWithAuth.post(BASE_URL, review);

    return response.data;
  },

  async getProductReviews(productId: number) {
    const response = await axiosWithAuth.get<IReview[]>(
      `${BASE_URL}/product/${productId}`
    );

    return response.data;
  },

  async patchReview(patchData: IReviewPatch, reviewId: number) {
    const response = await axiosWithAuth.patch(
      `${BASE_URL}/${reviewId}`,
      patchData
    );

    return response.data;
  },
};
