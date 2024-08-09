"use client";

import React, { FC } from "react";
import { useAppDispatch } from "@/store";
import { removeTokens } from "@/services";
import { emptyCart } from "@/store/slices/cart";
import { emptyFavorite } from "@/store/slices/favorite";
import { logout } from "@/store/slices/user";

export const ProfileUpdateTop: FC = () => {
  const dispatch = useAppDispatch();

  const handleExit = () => {
    dispatch(emptyCart());
    dispatch(emptyFavorite());
    dispatch(logout());

    removeTokens();
  };

  return (
    <div className=" flex justify-end mb-[20px]">
      <button
        className=" border-[1px] border-red-500 text-red-500 border-solid rounded-lg px-[10px] py-[5px] bg-white hover:bg-red-500 hover:text-white"
        onClick={() => handleExit()}
      >
        Выйти из аккаунта
      </button>
    </div>
  );
};
