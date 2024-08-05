"use client";

import React, { FC, useState } from "react";
import { SearchList } from "./SearchList";
import { HeaderImage } from "../HeaderImage";
import { type IHeaderSearchModalProps } from "../@types";

export const HeaderSearchModal: FC<IHeaderSearchModalProps> = ({
  setIsModalActive,
}) => {
  const [search, setSearch] = useState("");

  return (
    <>
      <header className=" absolute top-0 left-0 w-[100vw] bg-white h-[8vh] flex items-center justify-center">
        <div className=" rounded-lg p-[10px] bg-gray-100 w-[45%] flex gap-[5px]">
          <HeaderImage type="search" />
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            type="text"
            className=" bg-gray-100 w-[50vw] focus:outline-none"
          />
        </div>
      </header>
      <SearchList search={search} setIsModalActive={setIsModalActive} />
    </>
  );
};
