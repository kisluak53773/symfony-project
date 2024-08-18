"use client";

import React, { FC } from "react";
import { useSelector } from "react-redux";
import { getFavoriteProducts } from "@/store/slices/favorite";
import { FavoriteItem } from "./FavoriteItem";

export const FavoriteList: FC = () => {
  const favoriteProducts = useSelector(getFavoriteProducts);

  return (
    <>
      {favoriteProducts.length === 0 ? (
        <section className="w-full h-[80vh] flex flex-col items-center justify-center">
          <h1 className=" text-[20px] font-medium">
            У вас нет избранных товаров
          </h1>
          <p className="text-[18px]">
            Ищите товары в каталоге и поиске, смотрите интересные подборки на
            главной
          </p>
        </section>
      ) : (
        <section>
          <ul>
            {favoriteProducts.map((item) => (
              <FavoriteItem key={item.id} favoriteProduct={item} />
            ))}
          </ul>
        </section>
      )}
    </>
  );
};
