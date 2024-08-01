"use client";

import React, { FC, useState } from "react";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { HiddenCartItem } from "./CartItem";
import { IoBagOutline } from "react-icons/io5";
import { Modal } from "../Modal";
import { CartModal } from "./CartModal";
import { IoReloadOutline } from "react-icons/io5";
import Link from "next/link";

export const CartMenu: FC = () => {
  const productsInCart = useSelector(getCartProducts);
  const totalPrice = productsInCart
    ? productsInCart.reduce(
        (acc, item) => acc + parseFloat(item.price) * item.quantity,
        0
      )
    : 0;
  const [isModelActive, setIsModelActive] = useState(false);

  return (
    <aside className=" flex flex-col border-l-[1px] px-[5px] border-l-gray-400 bg-white border-solid fixed right-0 top-0 z-20 h-[100vh]">
      {productsInCart.length > 0 ? (
        <>
          <button
            onClick={() => setIsModelActive(!isModelActive)}
            className="mt-[8vh] h-full flex"
          >
            <ul className="overflow-auto">
              <li>
                {productsInCart.map((item) => (
                  <HiddenCartItem key={item.id} product={item} />
                ))}
              </li>
            </ul>
          </button>
          <div className=" border-t-[1px] border-solid border-gray-400 mt-auto">
            <p className="text-[14px] my-[10px] text-center">
              {totalPrice} руб.
            </p>
            <Link
              href="/order"
              className="flex items-center justify-center mt-auto mb-[20px] bg-blue-500 hover:bg-blue-300 py-[5px] rounded-lg w-[56px] h-[34px]"
            >
              <IoBagOutline size={25} color="white" />
            </Link>
          </div>
        </>
      ) : (
        <>
          <button
            onClick={() => setIsModelActive(!isModelActive)}
            className="h-full"
          />
          <div className=" border-t-[1px] border-solid border-gray-400 mt-auto pt-[20px]">
            <button className="flex items-center justify-center mt-auto mb-[20px] bg-gray-200 hover:bg-gray-300 py-[5px] rounded-lg w-[56px] h-[34px]">
              <IoReloadOutline size={25} color="black" />
            </button>
          </div>
        </>
      )}
      {isModelActive && (
        <Modal
          setIsModelActive={setIsModelActive}
          classes={{
            modalWindow: "!absolute right-0 ml-auto",
          }}
        >
          <CartModal />
        </Modal>
      )}
    </aside>
  );
};
