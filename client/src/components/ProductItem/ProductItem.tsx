"use client";

import React, { FC } from "react";
import { type IProductItemProps } from "@/types";
import { FiPlus } from "react-icons/fi";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { useAppDispatch } from "@/store";
import {
  addToCart,
  incrementQuantity,
  decrementQuantity,
} from "@/store/slices/cart";
import { FiMinus } from "react-icons/fi";
import { productImagePathConverter } from "@/services";

export const ProductItem: FC<IProductItemProps> = ({ product }) => {
  const isItemInCart = useSelector(getCartProducts).find(
    (item) => item.id === product.id
  );
  const dispatch = useAppDispatch();

  const handleAdd = () => {
    const newProduct = {
      ...product,
      quantity: 1,
      price: product.vendorProducts[0].price,
      vendorId: product.vendorProducts[0].vendorId,
    };

    dispatch(addToCart(newProduct));
  };

  return (
    <section className=" flex flex-col shadow-xl p-[10px] rounded-lg my-[10px]">
      <img
        className=" w-full"
        src={productImagePathConverter(product.image)}
        width={200}
        height={400}
        alt="Картинка продукта"
      />
      <p className="font-semibold text-red-500 text-[18px]">
        {product.vendorProducts[0].price} р.
      </p>
      <p>{product.title}</p>
      <p className=" text-[13px] text-gray-400 mb-[30px]">{product.weight}</p>
      {isItemInCart ? (
        <div className=" flex w-full justify-between h-[34px]">
          <button onClick={() => dispatch(decrementQuantity(isItemInCart))}>
            <FiMinus size={20} color="black" />
          </button>
          <span>{isItemInCart.quantity} шт.</span>
          <button onClick={() => dispatch(incrementQuantity(isItemInCart))}>
            <FiPlus size={20} color="black" />
          </button>
        </div>
      ) : (
        <button
          onClick={handleAdd}
          className="flex items-center justify-center bg-blue-500 hover:bg-blue-300 rounded-lg w-[64px] h-[34px]"
        >
          <FiPlus size={30} color="white" />
        </button>
      )}
    </section>
  );
};
