"use client";

import React, { FC } from "react";
import { RxCross2 } from "react-icons/rx";

export const Canceled: FC = () => {
  return (
    <div className=" flex items-center bg-red-500 text-white rounded-[5px] py-[5px] px-[10px]">
      <RxCross2 color="white" />
      <span className=" ml-[10px]">Отменен</span>
    </div>
  );
};
