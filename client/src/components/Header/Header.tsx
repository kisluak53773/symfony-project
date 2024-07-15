import React, { FC } from "react";
import { HeaderLinks } from "./HeaderLinks";
import { HeaderSeach } from "./HeaderSeach";
import Link from "next/link";

export const Header: FC = () => {
  return (
    <header className=" h-[8vh] sticky top-0 w-full z-20 flex border-b-[1px] border-b-gray-400 gap-[20px] items-center justify-center ">
      <h1>
        <Link href="/">Logo placeholder</Link>
      </h1>
      <HeaderSeach />
      <HeaderLinks />
    </header>
  );
};
