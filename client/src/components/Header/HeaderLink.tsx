import React, { FC } from "react";
import { type IHeaderLinkProps } from "./@types";
import { HeaderImage } from "./HeaderImage";
import Link from "next/link";

export const HeaderLink: FC<IHeaderLinkProps> = ({ href, title, img }) => {
  return (
    <li>
      <Link href={href} className=" flex flex-col items-center justify-center">
        <HeaderImage type={img} />
        <p>{title}</p>
      </Link>
    </li>
  );
};
