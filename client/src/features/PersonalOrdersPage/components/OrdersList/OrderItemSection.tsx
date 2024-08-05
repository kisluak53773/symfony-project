import React, { FC } from "react";
import { type IOrderItemSectionProps } from "../../types";

export const OrderItemSection: FC<IOrderItemSectionProps> = ({
  heading,
  data,
}) => {
  return (
    <li className=" grid grid-cols-2 my-[20px]">
      <p className=" mr-[20px] text-gray-500">{heading}</p>
      <p>{data}</p>
    </li>
  );
};
