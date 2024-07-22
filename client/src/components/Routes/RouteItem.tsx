"use client";

import { usePathname } from "next/navigation";
import React, { FC } from "react";
import Link from "next/link";
import { type IRouteItemProps } from "./@types";

export const RouteItem: FC<IRouteItemProps> = ({ href, title }) => {
  const path = usePathname();

  return (
    <li
      className={
        path === href
          ? " py-[5px] px-[10px] bg-button rounded-[5px]"
          : " py-[5px] px-[10px]"
      }
    >
      <Link href={href}>{title}</Link>
    </li>
  );
};
