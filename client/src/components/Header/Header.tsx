"use client";

import React, { FC, useEffect } from "react";
import { HeaderLinks } from "./HeaderLinks";
import { HeaderSeach } from "./HeaderSearch";
import Link from "next/link";
import { authService } from "@/services/auth";
import { useAppDispatch } from "@/store";
import { logout } from "@/store/slices/user";
import { removeTokens } from "@/services";

export const Header: FC = () => {
  const dispatch = useAppDispatch();

  useEffect(() => {
    async () => {
      try {
        await authService.refresh();
      } catch (error) {
        dispatch(logout());
        removeTokens();
      }
    };
  }, []);

  return (
    <header className=" h-[8vh] bg-white sticky top-0 w-full z-20 flex border-b-[1px] border-b-gray-400 gap-[20px] items-center justify-center ">
      <h1>
        <Link href="/">Logo placeholder</Link>
      </h1>
      <HeaderSeach />
      <HeaderLinks />
    </header>
  );
};
