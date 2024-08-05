"use client";

import React, { FC } from "react";
import { type ICartProductItemProps } from "../../types";
import { useAppDispatch } from "@/store";
import { productImagePathConverter } from "@/services";
import { FiPlus } from "react-icons/fi";
import { FiMinus } from "react-icons/fi";
import {
  increaseQuantity,
  decreaseQuantity,
  deleteProductFromCart,
} from "@/store/slices/cart";
import { FaRegTrashCan } from "react-icons/fa6";

export const CartProductItem: FC<ICartProductItemProps> = ({ product }) => {
  const dispatch = useAppDispatch();

  return (
    <li className="flex group/cartProduct gap-[20px] border-[1px] border-solid border-gray-400 rounded-lg py-[10px] px-[20px] relative">
      <img
        src={productImagePathConverter(product.productImage)}
        alt="Картиинка продукта"
        width={56}
        height={56}
        className=" w-[56px] h-[56px]"
      />
      <div className="flex flex-col w-full">
        <p>{product.productTitle}</p>
        <div className=" flex justify-between mt-[10px] w-full items-center">
          <span className="font-semibold text-red-500">
            {parseFloat(product.price) * product.quantity} р.
          </span>
          <div className=" flex w-[140px] justify-between items-center h-[34px] border-[1px] border-gray-300 border-solid rounded-lg">
            <button
              className="w-[80%] flex items-center justify-center"
              onClick={() =>
                dispatch(
                  decreaseQuantity({
                    vendorProductId: product.vendorProductId,
                    quantity: 1,
                  })
                )
              }
            >
              <FiMinus size={20} color="black" />
            </button>
            <span className="w-full text-center">{product.quantity} шт.</span>
            <button
              className=" w-[80%] flex items-center justify-center"
              onClick={() =>
                dispatch(
                  increaseQuantity({
                    vendorProductId: product.vendorProductId,
                    quantity: 1,
                  })
                )
              }
            >
              <FiPlus size={20} color="black" />
            </button>
          </div>
        </div>
        <div className=" flex justify-end">
          <p className="text-[12px] text-gray-500">{product.price} р. за шт.</p>
        </div>
      </div>
      <button
        onClick={() =>
          dispatch(
            deleteProductFromCart({ vendorProductId: product.vendorProductId })
          )
        }
        className="absolute right-[10px] top-[10px] hidden group-hover/cartProduct:block"
      >
        <FaRegTrashCan size={20} color="red" />
      </button>
    </li>
  );
};
