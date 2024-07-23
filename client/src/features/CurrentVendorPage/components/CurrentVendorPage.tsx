import React, { FC } from "react";
import { RoutePageWrapper } from "@/components/Routes";
import { VendorUpdateForm } from "./Form";

export const CurrentVendorPage: FC = () => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading="Данные вашего юр лица">
        <VendorUpdateForm />
      </RoutePageWrapper>
    </main>
  );
};
