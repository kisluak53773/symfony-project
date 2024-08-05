import React, { FC } from "react";
import { type IOrderDetailsProps } from "../../types";
import { DeliveryState } from "@/components/DeliveryState";
import { OrderItemSection } from "@/features/PersonalOrdersPage";
import { formatDateString } from "@/services";

export const OrderDetails: FC<IOrderDetailsProps> = ({ order }) => {
  return (
    <div className=" w-full rounded-[10px] drop-shadow-2xl shadow-2xl">
      <DeliveryState status={order.orderStatus} />
      <ul className="grid grid-cols-2 px-[10px]">
        <OrderItemSection
          heading="Дата оформления:"
          data={formatDateString(order.createdAt)}
        />
        <OrderItemSection
          heading="Доставка:"
          data={formatDateString(order.deliveryTime)}
        />
        <OrderItemSection heading="Адрес:" data={order.deliveryAddress} />
        <OrderItemSection heading="Способ оплаты:" data={order.paymentMethod} />
        <OrderItemSection
          heading="Стоймость заказа:"
          data={order.totalPrice + " руб."}
        />
      </ul>
    </div>
  );
};
