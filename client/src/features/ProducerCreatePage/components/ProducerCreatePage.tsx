import React, { FC } from "react";
import { ProducerForm } from "./Form";
import { RoutePageWrapper } from "@/components/Routes";

export const ProducerCreatePage: FC = () => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading="Создание производителя продукции">
        <ProducerForm />
      </RoutePageWrapper>
    </main>
  );
};
