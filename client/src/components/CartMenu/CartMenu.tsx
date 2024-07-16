"use client";

import React, { FC, useState } from "react";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { HiddenCartItem } from "./CartItem";
import { IoBagOutline } from "react-icons/io5";
import { Modal } from "../Modal";
import { CartModal } from "./CartModal";

export const CartMenu: FC = () => {
  const productsInCart = useSelector(getCartProducts);
  const totalPrice = productsInCart
    ? productsInCart.reduce((acc, item) => acc + item.price * item.quantity, 0)
    : 0;
  const [isModelActive, setIsModelActive] = useState(true);

  return (
    <aside className=" flex flex-col border-l-[1px] px-[5px] border-l-gray-400 bg-white border-solid absolute right-0 top-0 z-20 h-[100vh]">
      {productsInCart && (
        <>
          <ul className="mt-[8vh] overflow-auto">
            <li>
              {productsInCart.map((item) => (
                <HiddenCartItem key={item.id} product={item} />
              ))}
            </li>
          </ul>
          <div className=" border-t-[1px] border-solid border-gray-400 mt-auto">
            <p className="text-[14px] my-[10px]">{totalPrice} руб.</p>
            <button className="flex items-center justify-center mt-auto mb-[20px] bg-blue-500 hover:bg-blue-300 py-[5px] rounded-lg w-[56px] h-[34px]">
              <IoBagOutline size={25} color="white" />
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
