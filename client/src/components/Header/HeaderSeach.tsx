import React, { FC } from "react";
import { HeaderImage } from "./HeaderImage";

export const HeaderSeach: FC = () => {
  return (
    <div className=" rounded-lg p-[10px] bg-gray-100 w-[45%] flex gap-[5px]">
      <HeaderImage type="search" />
      <input type="text" className=" w-full bg-gray-100 focus:outline-none" />
    </div>
  );
};
