"use client";

import React, { FC } from "react";
import { FaTruckMoving } from "react-icons/fa";

export const OnTheWay: FC = () => {
  return (
    <div className=" flex items-center bg-orange-300 text-white rounded-[5px] py-[5px] px-[10px]">
      <FaTruckMoving color="white" />
      <span className=" ml-[10px]">В процессе доставки</span>
    </div>
  );
};
