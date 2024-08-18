"use client";

import React, { FC } from "react";
import { CartProductItem } from "./CartProductItem";
import { getCartProducts, deleteAllProducstInCart } from "@/store/slices/cart";
import { useSelector } from "react-redux";
import { useAppDispatch } from "@/store";

export const CartProductList: FC = () => {
  const products = useSelector(getCartProducts);
  const totalPrice = products
    ? products.reduce(
        (acc, item) => acc + parseFloat(item.price) * item.quantity,
        0
      )
    : 0;
  const dispatch = useAppDispatch();

  return (
    <section className="flex items-center justify-center flex-col">
      <h2 className=" font-semibold text-[24px]">Корзина</h2>
      {products.length > 0 ? (
        <>
          <div className=" flex justify-end w-full">
            <button
              className="text-gray-500 hover:text-black transition-all py-[5px] px-[10px]rounded-lg"
              onClick={() => dispatch(deleteAllProducstInCart())}
            >
              Очистить корзину
            </button>
          </div>
          <ul className=" w-full mt-[10px]">
            {products.map((item) => (
              <CartProductItem key={item.id} product={item} />
            ))}
          </ul>
          <div className="w-full mt-[10px]">
            <p className=" font-semibold">Итого: {totalPrice}</p>
          </div>
        </>
      ) : (
        <div className="w-full h-[80vh] flex flex-col items-center justify-center">
          <h1 className=" text-[20px] font-medium">Корзина пуста</h1>
          <p className="text-[18px]">
            Ищите товары в каталоге и поиске, смотрите интересные подборки на
            главной
          </p>
        </div>
      )}
    </section>
  );
};
