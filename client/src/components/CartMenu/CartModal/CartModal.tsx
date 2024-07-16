"use client";

import React, { FC } from "react";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { CartModalItem } from "../CartItem";

export const CartModal: FC = () => {
  const productsInCart = useSelector(getCartProducts);

  return (
    <article className=" bg-white h-[100vh] w-[26vw] ml-auto">
      <div className=" flex justify-between border-b-[1px] border-b-gray-300 p-[20px] border-solid">
        <h1 className=" font-semibold text-[25px]">Корзина</h1>
        <button className=" text-gray-400">Удалить все</button>
      </div>
      {productsInCart ? (
        <ul>
          {productsInCart.map((item) => (
            <li key={item.id}>
              <CartModalItem product={item} />
            </li>
          ))}
        </ul>
      ) : (
        <div>
          <h1 className=" font-medium text-[20px]">Корзина пуста</h1>
        </div>
      )}
    </article>
  );
};
