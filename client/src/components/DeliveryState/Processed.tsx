import React from "react";
import { FaBoxes } from "react-icons/fa";

export const Processed = () => {
  return (
    <div className=" flex items-center bg-gray-300 text-white rounded-[5px] py-[5px] px-[10px]">
      <FaBoxes color="white" />
      <span className=" ml-[10px]">В обработке</span>
    </div>
  );
};
