"use client";

import React, { FC } from "react";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { CartModalItem } from "../CartItem";
import { IoBagOutline } from "react-icons/io5";
import { useAppDispatch } from "@/store";
import { removeAllFromCart } from "@/store/slices/cart";
import { IoReloadOutline } from "react-icons/io5";

export const CartModal: FC = () => {
  const productsInCart = useSelector(getCartProducts);
  const totalPrice = productsInCart
    ? productsInCart.reduce((acc, item) => acc + item.price * item.quantity, 0)
    : 0;
  const dispatch = useAppDispatch();

  return (
    <article className=" flex flex-col bg-white h-[100vh] w-[26vw] ml-auto">
      <div className=" flex justify-between border-b-[1px] border-b-gray-300 p-[20px] border-solid">
        <h1 className=" font-semibold text-[25px]">Корзина</h1>
        {productsInCart.length > 0 && (
          <button
            onClick={() => dispatch(removeAllFromCart())}
            className=" text-gray-500 hover:text-black transition-all"
          >
            Удалить все
          </button>
        )}
      </div>
      {productsInCart.length > 0 ? (
        <>
          <ul className="px-[20px]">
            {productsInCart.map((item) => (
              <li
                className="border-b-[1px] py-[10px] border-b-gray-400 border-solid"
                key={item.id}
              >
                <CartModalItem product={item} />
              </li>
            ))}
          </ul>
          <div className=" mt-auto border-t-[1px] border-gray-400 border-solid p-[20px]">
            <div className="flex justify-between mb-[20px]">
              <span className=" text-[12p]">
                {productsInCart.length} товара
              </span>
              <span className=" font-semibold text-[18px]">
                {totalPrice} р.
              </span>
            </div>
            <button className=" rounded-lg flex items-center justify-center h-[45px] text-white bg-blue-500 hover:bg-blue-300 w-full">
              <IoBagOutline size={20} color="white" />
              <span className="text-[15px] ml-[10px]">Перейти в корзину</span>
            </button>
          </div>
        </>
      ) : (
        <>
          <div className=" text-[16px] h-full flex flex-col items-center justify-center">
            <h1 className=" font-medium text-[20px]">Корзина пуста</h1>
            <p className="text-center">
              Ищите товары в каталоге и поиске, смотрите интересные подборки на
              главной
            </p>
          </div>
          <div className=" mt-auto border-t-[1px] border-gray-400 border-solid p-[20px]">
            <button className=" rounded-lg flex items-center justify-center h-[45px] bg-gray-200 hover:bg-gray-300 w-full">
              <IoReloadOutline size={20} color="black" />
              <span className="text-[15px] ml-[10px]">Повторить заказ</span>
            </button>
          </div>
        </>
      )}
    </article>
  );
};
