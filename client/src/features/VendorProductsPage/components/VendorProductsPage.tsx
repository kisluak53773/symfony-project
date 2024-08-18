import React, { FC } from "react";
import { RoutePageWrapper } from "@/components/Routes";
import { VendorProductList } from "./Products";

export const VendorProductsPage: FC = () => {
  return (
    <main className="w-full">
      <RoutePageWrapper heading="Все ваши товары">
        <VendorProductList />
      </RoutePageWrapper>
    </main>
  );
};
