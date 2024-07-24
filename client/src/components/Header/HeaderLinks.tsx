"use client";

import React, { FC } from "react";
import { HEADER_LINKS } from "@/constants/headerLinks";
import { HeaderLink } from "./HeaderLink";
import { useSelector } from "react-redux";
import { getUser } from "@/store/slices/user";
import { ROLES } from "@/constants/projectConstants";

export const HeaderLinks: FC = () => {
  const user = useSelector(getUser);

  return (
    <nav>
      <ul className=" flex gap-[10px] text-[12px]">
        {user && user.roles.includes(ROLES.ROLE_VENDOR) ? (
          <HeaderLink
            href="/vendor/product"
            title="Панель продавца"
            img="vendor"
          />
        ) : (
          <HeaderLink href="/auth" title="Войти" img="person" />
        )}
        {HEADER_LINKS.map((item) => (
          <HeaderLink
            key={item.id}
            href={item.href}
            img={item.img}
            title={item.title}
          />
        ))}
      </ul>
    </nav>
  );
};
