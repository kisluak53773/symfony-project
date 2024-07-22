import React, { FC } from "react";
import { TypeForm } from "./Form";
import { RoutePageWrapper } from "@/components/Routes";

export const TypeCreatePage: FC = () => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading="Создание нового типа продукции">
        <TypeForm />
      </RoutePageWrapper>
    </main>
  );
};
