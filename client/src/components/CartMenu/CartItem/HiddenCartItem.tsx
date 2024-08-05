import React, { FC } from "react";
import { type ICartItemProps } from "./@types";
import { productImagePathConverter } from "@/services";

export const HiddenCartItem: FC<ICartItemProps> = ({ product }) => {
  return (
    <section
      style={{
        backgroundImage: `url(${productImagePathConverter(
          product.productImage
        )})`,
      }}
      className=" w-[56px] bg-contain h-[66px] flex items-end"
    >
      <span className=" py-[1px] text-[13px] px-[2px] rounded-lg bg-white">
        {product.quantity} шт.
      </span>
    </section>
  );
};
