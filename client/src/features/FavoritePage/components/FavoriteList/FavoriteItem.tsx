"use client";

import React, { FC } from "react";
import { type IFavoriteItemProps } from "../../types";
import { productImagePathConverter } from "@/services";
import { FaRegTrashCan } from "react-icons/fa6";
import { useAppDispatch } from "@/store";
import { deleteProductFromFavorite } from "@/store/slices/favorite";

export const FavoriteItem: FC<IFavoriteItemProps> = ({ favoriteProduct }) => {
  const dispatch = useAppDispatch();

  return (
    <li className="flex group/favoriteProduct relative items-center rounded-lg border-[1px] border-gray-300 border-solid px-[20px] py-[10px]">
      <img
        src={productImagePathConverter(favoriteProduct.image)}
        width={400}
        height={200}
        className="w-[150px] h-[150px] mr-[10px]"
      />
      <div className=" flex flex-col gap-[20px]">
        <h1 className="text-[20px] font-semibold">{favoriteProduct.title}</h1>
        <div className="flex gap-[20px]">
          <span>Еденица измерения: {favoriteProduct.weight}</span>
          <span>
            Цена за еденицу товара: {favoriteProduct.vendorProducts[0].price}{" "}
            руб.
          </span>
          <span>
            На складе: {favoriteProduct.vendorProducts[0].quantity} шт.
          </span>
        </div>
      </div>
      <button
        onClick={() =>
          dispatch(deleteProductFromFavorite({ productId: favoriteProduct.id }))
        }
        className="absolute right-[10px] top-[10px] hidden group-hover/favoriteProduct:block"
      >
        <FaRegTrashCan size={25} color="red" />
      </button>
    </li>
  );
};
