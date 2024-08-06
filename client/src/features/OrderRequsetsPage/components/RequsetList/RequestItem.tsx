"use client";

import React, { FC } from "react";
import { OrderItemSection } from "@/features/PersonalOrdersPage";
import Link from "next/link";
import { DeliveryState } from "@/components/DeliveryState";
import { type IRequestItemProps } from "../../types";
import { formatDateString } from "@/services";

export const RequestItem: FC<IRequestItemProps> = ({ order }) => {
  return (
    <li className=" group/userOrder relative drop-shadow-2xl shadow-2xl py-[15px] px-[10px] rounded-[10px]">
      <Link href={`/vendor/ordersRequset/${order.id}`}>
        <DeliveryState status={order.orderStatus} />
        <h1 className=" text-[24px] font-bold mt-[15px]">
          Доставка №{order.id}
        </h1>
        <ul>
          <OrderItemSection
            heading="Дата оформления:"
            data={formatDateString(order.createdAt)}
          />
          <OrderItemSection
            heading="Доставка:"
            data={formatDateString(order.deliveryTime)}
          />
          <OrderItemSection heading="Адрес:" data={order.deliveryAddress} />
          <OrderItemSection
            heading="Способ оплаты:"
            data={order.paymentMethod}
          />
          <OrderItemSection
            heading="Стоймость заказа:"
            data={order.totalPrice + " руб."}
          />
        </ul>
      </Link>
    </li>
  );
};
