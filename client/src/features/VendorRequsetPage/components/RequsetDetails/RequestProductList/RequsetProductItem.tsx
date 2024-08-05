import React, { FC } from "react";
import { IRequestProductItemProps } from "../../../types";
import { productImagePathConverter } from "@/services";

export const RequsetProductItem: FC<IRequestProductItemProps> = ({
  product,
}) => {
  return (
    <li className=" flex rounded-lg p-[10px] gap-[20px] border-[1px] border-gray-400 border-solid">
      <img
        src={productImagePathConverter(product.productImage)}
        width={150}
        height={100}
        alt="Image of product"
        className="w-[100px] h-[100px]"
      />
      <div className=" flex flex-col gap-[20px]">
        <p className=" font-semibold">{product.productTitle}</p>
        <div className=" flex gap-[20px]">
          <span>Цена за едеденицу товара: {product.price}</span>
          <span>{product.productWeight} за шт.</span>
          <span>Количество: {product.quantity}</span>
        </div>
      </div>
    </li>
  );
};
