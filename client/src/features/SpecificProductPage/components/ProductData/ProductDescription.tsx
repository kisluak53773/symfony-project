"use client";

import React, { FC } from "react";
import { type IProductDescriptionProsp } from "../../types";
import { productImagePathConverter } from "@/services";
import { Rating } from "@/components/Rating";
import { typeImagePathConverter } from "@/services";
import { VendorList } from "./VendorList";

export const ProductDescription: FC<IProductDescriptionProsp> = ({
  product,
  setProduct,
}) => {
  return (
    <article>
      <h1 className=" text-[40px] font-bold">
        {product.title + " " + product.weight}
      </h1>
      <div className=" flex gap-[20px]">
        <img
          className=" w-[28vw] h-[60vh]"
          src={productImagePathConverter(product.image)}
          width={200}
          height={400}
          alt="Картинка продукта"
        />
        <div className=" gap-4 flex flex-col mt-[20px]">
          <section className=" flex items-center">
            <Rating rating={product.averageRating} />
            <span className=" ml-2">{product.averageRating}</span>
          </section>
          <section>
            <h2 className=" text-[12px] font-semibold text-gray-400">Состав</h2>
            <p className=" text-[14px]">{product.compound}</p>
          </section>
          <section className=" flex items-center gap-2">
            <h2 className=" text-[12px] font-semibold text-gray-400">Тип:</h2>
            <span className=" text-[14px]">{product.type.title}</span>
            <img
              className=" w-[50px] h-[25px]"
              src={typeImagePathConverter(product.type.image)}
              width={100}
              height={100}
              alt="Картинка продукта"
            />
          </section>
        </div>
        <VendorList product={product} setProduct={setProduct} />
      </div>
    </article>
  );
};
