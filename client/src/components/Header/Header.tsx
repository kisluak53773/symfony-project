"use client";

import React, { FC, useEffect } from "react";
import { HeaderLinks } from "./HeaderLinks";
import { HeaderSeach } from "./HeaderSearch";
import Link from "next/link";
import { authService } from "@/services/auth";
import { useAppDispatch } from "@/store";
import { login, logout } from "@/store/slices/user";
import { removeTokens } from "@/services";
import { getCart, emptyCart } from "@/store/slices/cart";
import { emptyFavorite } from "@/store/slices/favorite";
import { getHeaderLinks } from "@/services";
import { useSelector } from "react-redux";
import { getIsAuthorized } from "@/store/slices/user";
import { fetchFavoriteProducts } from "@/store/slices/favorite";

export const Header: FC = () => {
  const dispatch = useAppDispatch();
  const links = getHeaderLinks();
  const isAuthorized = useSelector(getIsAuthorized);

  useEffect(() => {
    (async () => {
      try {
        await authService.refresh();
        dispatch(getCart());
        dispatch(fetchFavoriteProducts());
        dispatch(login());
      } catch (error) {
        dispatch(logout());
        dispatch(emptyCart());
        dispatch(emptyFavorite());
        removeTokens();
      }
    })();
  }, []);

  return (
    <header className=" h-[8vh] bg-white sticky top-0 w-full z-20 flex border-b-[1px] border-b-gray-400 gap-[20px] items-center justify-center ">
      <h1>
        <Link href="/">Logo placeholder</Link>
      </h1>
      <HeaderSeach />
      <HeaderLinks links={links} />
    </header>
  );
};
