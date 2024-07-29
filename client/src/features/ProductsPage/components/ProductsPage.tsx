import React, { FC } from "react";
import { ProductsList } from "./ProductsList";

export const ProductsPage: FC = () => {
  return (
    <main className=" px-[60px]">
      <ProductsList />
    </main>
  );
};
