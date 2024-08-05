"use client";

import React, { FC } from "react";
import { type IRequestProductListProps } from "../../../types";
import { RequsetProductItem } from "./RequsetProductItem";

export const RequestProductList: FC<IRequestProductListProps> = ({
  products,
}) => {
  return (
    <article className=" mt-[20px]">
      <h1 className=" font-semibold text-[20px]">
        Продукты, которые у вас заказали
      </h1>
      <ul className=" mt-[20px]">
        {products.map((item) => (
          <RequsetProductItem key={item.id} product={item} />
        ))}
      </ul>
    </article>
  );
};
