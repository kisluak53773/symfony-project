import React, { FC } from "react";
import { RoutePageWrapper } from "@/components/Routes";
import { FavoriteList } from "./FavoriteList";

export const FavoritePage: FC = () => {
  return (
    <main className="w-full">
      <RoutePageWrapper heading="Избранные товары">
        <FavoriteList />
      </RoutePageWrapper>
    </main>
  );
};
