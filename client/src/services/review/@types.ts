export interface IReview {
  id: number;
  rating: number;
  comment?: string;
  createdAt: string;
  updatedAt: string;
}

export interface IReviewPatch extends Omit<IReview, "id"> {}

export interface IReviewCreate extends Omit<IReview, "id"> {
  productId: number;
}
