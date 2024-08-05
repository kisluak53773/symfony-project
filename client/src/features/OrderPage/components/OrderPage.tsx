import React, { FC } from "react";
import { CartProductList } from "./CartProductList";
import { OrderForm } from "./Form";

export const OrderPage: FC = () => {
  return (
    <main className="pt-[10px] px-[100px]">
      <CartProductList />
      <OrderForm />
    </main>
  );
};
