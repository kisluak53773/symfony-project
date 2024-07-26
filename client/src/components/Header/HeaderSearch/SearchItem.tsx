"use client";

import React, { FC } from "react";
import { type ISearchItemProps } from "../@types";
import { productImagePathConverter } from "@/services";

export const SearchItem: FC<ISearchItemProps> = ({ product }) => {
  return (
    <li className=" flex items-center justify-center gap-[20px] border-b-[1px] border-b-gray-400 border-solid px-[20px]">
      <img
        src={productImagePathConverter(product.image)}
        width={150}
        height={100}
        alt="Image of product"
        className="w-[100px] h-[100px]"
      />
      <p className=" font-semibold text-center">{product.title}</p>
    </li>
  );
};
