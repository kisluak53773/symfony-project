import React, { FC } from "react";
import { VenndorForm } from "./Form";
import { RoutePageWrapper } from "@/components/Routes";

export const ProductCreatePage: FC = () => {
  return (
    <main className="w-full">
      <RoutePageWrapper heading="Создание нового продукта">
        <VenndorForm />
      </RoutePageWrapper>
    </main>
  );
};
