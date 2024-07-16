"use client";

import React, { FC } from "react";
import { type IProductItemProps } from "@/types";
import Image from "next/image";
import { GoPlus } from "react-icons/go";

export const ProductItem: FC<IProductItemProps> = ({ product }) => {
  return (
    <section className=" flex flex-col shadow-xl p-[10px] rounded-lg my-[10px]">
      <Image
        className=" w-full"
        src={"http://127.0.0.1:8000/images/products/" + product.image}
        width={200}
        height={400}
        alt="Картинка продукта"
      />
      <p className="font-semibold text-red-500 text-[18px]">
        {product.price} р.
      </p>
      <p>{product.title}</p>
      <p className=" text-[13px] text-gray-400">{product.weight}</p>
      <button className="flex items-center justify-center bg-blue-500 hover:bg-blue-300 rounded-lg mt-[30px] w-[64px] h-[34px]">
        <GoPlus size={30} color="white" />
      </button>
    </section>
  );
};
