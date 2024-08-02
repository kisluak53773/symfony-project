import React, { FC } from "react";
import { OrdersList } from "./OrdersList";

export const PersonalOrdersPage: FC = () => {
  return (
    <main className=" py-[20px]">
      <OrdersList />
    </main>
  );
};
