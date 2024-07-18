"use client";

import React, { FC } from "react";
import { type ICartItemProps } from "./@types";
import { productImagePathConverter } from "@/services";
import { useAppDispatch } from "@/store";
import { FiPlus } from "react-icons/fi";
import { FiMinus } from "react-icons/fi";
import { incrementQuantity, decrementQuantity } from "@/store/slices/cart";

export const CartModalItem: FC<ICartItemProps> = ({ product }) => {
  const dispatch = useAppDispatch();

  return (
    <section className="flex gap-[20px]">
      <img
        src={productImagePathConverter(product.image)}
        alt="Картиинка продукта"
        width={56}
        height={56}
        className=" w-[56px] h-[56px]"
      />
      <div className="flex flex-col w-full">
        <p>{product.title}</p>
        <div className=" flex justify-between mt-[10px] w-full items-center">
          <span className="font-semibold text-red-500">
            {parseFloat(product.price) * product.quantity} р.
          </span>
          <div className=" flex w-[140px] justify-between items-center h-[34px] border-[1px] border-gray-300 border-solid rounded-lg">
            <button
              className="w-[80%] flex items-center justify-center"
              onClick={() => dispatch(decrementQuantity(product))}
            >
              <FiMinus size={20} color="black" />
            </button>
            <span className="w-full text-center">{product.quantity} шт.</span>
            <button
              className=" w-[80%] flex items-center justify-center"
              onClick={() => dispatch(incrementQuantity(product))}
            >
              <FiPlus size={20} color="black" />
            </button>
          </div>
        </div>
        <div className=" flex justify-end">
          <p className="text-[12px] text-gray-500">{product.price} р. за шт.</p>
        </div>
      </div>
    </section>
  );
};
