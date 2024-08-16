import React, { FC } from "react";
import { type IRatingProps } from "./@types";
import { IoIosStar } from "react-icons/io";

export const Rating: FC<IRatingProps> = ({ rating }) => {
  const array = Array(5).fill("data");

  return (
    <>
      {array.map((_, index) => {
        return index < rating ? (
          <IoIosStar key={index} size={14} color="yellow" />
        ) : (
          <IoIosStar key={index} size={14} color="gray" />
        );
      })}
    </>
  );
};
