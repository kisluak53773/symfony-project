import React, { FC } from "react";
import { RoutePageWrapper } from "@/components/Routes";
import { ExistingProductList } from "./ExistingProducts";

export const VendorProductAddPage: FC = () => {
  return (
    <main className="w-full">
      <RoutePageWrapper heading="Товары которые вы можете начать продавать">
        <ExistingProductList />
      </RoutePageWrapper>
    </main>
  );
};
