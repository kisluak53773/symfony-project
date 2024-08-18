import React, { FC } from "react";
import { type ISpecificProductPageProps } from "../types";
import { ProductData } from "./ProductData";
import { Reviews } from "./Reviews";

export const SpecificProductPage: FC<ISpecificProductPageProps> = ({
  productId,
}) => {
  return (
    <main className="px-[120px] pt-[20px]">
      <ProductData productId={productId} />
      <Reviews productId={productId} />
    </main>
  );
};
