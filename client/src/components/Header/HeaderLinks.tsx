"use client";

import React, { FC } from "react";
import { HeaderLink } from "./HeaderLink";
import { type IHeaderLinksProps } from "./@types";

export const HeaderLinks: FC<IHeaderLinksProps> = ({ links }) => {
  return (
    <nav>
      <ul className=" flex gap-[10px] text-[12px]">
        {links.map((item) => (
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
