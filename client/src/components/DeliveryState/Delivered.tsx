"use client";

import React, { FC } from "react";
import { FaHouse } from "react-icons/fa6";

export const Delivered: FC = () => {
  return (
    <div className=" flex items-center bg-green-400 text-white rounded-[5px] py-[5px] px-[10px]">
      <FaHouse color="white" />
      <span className=" ml-[10px]">Доставлен</span>
    </div>
  );
};
