import React, { FC } from "react";
import { HEADER_LINKS } from "@/constants/headerLinks";
import Link from "next/link";
import { HeaderImage } from "./HeaderImage";

export const HeaderLinks: FC = () => {
  return (
    <nav>
      <ul className=" flex gap-[10px] text-[12px]">
        {HEADER_LINKS.map((item) => (
          <li key={item.id}>
            <Link
              href={item.href}
              className=" flex flex-col items-center justify-center"
            >
              <HeaderImage type={item.img} />
              <p>{item.title}</p>
            </Link>
          </li>
        ))}
      </ul>
    </nav>
  );
};
