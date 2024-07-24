import React, { FC } from "react";
import { type IHeaderImageProps } from "./@types";
import { IoBagOutline } from "react-icons/io5";
import { FaRegHeart } from "react-icons/fa";
import { IoPersonOutline } from "react-icons/io5";
import { IoIosMenu } from "react-icons/io";
import { BsBoxSeam } from "react-icons/bs";
import { CiSearch } from "react-icons/ci";
import { TbDeviceIpadMinus } from "react-icons/tb";

export const HeaderImage: FC<IHeaderImageProps> = ({ type }) => {
  switch (type) {
    case "favorite":
      return <FaRegHeart color="gray" size={25} />;
    case "cart":
      return <IoBagOutline color="gray" size={25} />;
    case "person":
      return <IoPersonOutline color="gray" size={25} />;
    case "menu":
      return <IoIosMenu color="gray" size={25} />;
    case "orders":
      return <BsBoxSeam color="gray" size={25} />;
    case "search":
      return <CiSearch color="gray" size={25} />;
    case "vendor":
      return <TbDeviceIpadMinus color="gray" size={25} />;
  }
};
