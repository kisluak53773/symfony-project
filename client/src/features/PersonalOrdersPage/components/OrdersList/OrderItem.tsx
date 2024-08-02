"use client";

import React, { FC } from "react";
import { type IOrderItemProps } from "../../types";
import { OrderItemSection } from "./OrderItemSection";
import Link from "next/link";
import { DeliveryState } from "@/components/DeliveryState";
import { IoMdCloseCircleOutline } from "react-icons/io";
import { orderService } from "@/services/order";
import { ORDER_STATUSES } from "@/constants";

export const OrderItem: FC<IOrderItemProps> = ({
  order,
  handleOrderUpdate,
}) => {
  const handleClick = async () => {
    try {
      await orderService.cancelOrder(order.id);
      handleOrderUpdate({
        ...order,
        orderStatus: ORDER_STATUSES.ORDER_CANCELED,
      });
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <li className=" group/userOrder relative drop-shadow-2xl shadow-2xl py-[15px] px-[10px] rounded-[10px]">
      <Link href={`/orders/${order.id}`}>
        <DeliveryState status={order.orderStatus} />
        <h1 className=" text-[24px] font-bold mt-[15px]">
          Доставка №{order.id}
        </h1>
        <ul>
          <OrderItemSection heading="Дата оформления:" data={order.createdAt} />
          <OrderItemSection heading="Доставка:" data={order.deliveryTime} />
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
      {order.orderStatus === ORDER_STATUSES.ORDER_PROCESSED && (
        <button
          onClick={handleClick}
          className="absolute right-[10px] top-[20px] hidden group-hover/userOrder:block"
        >
          <IoMdCloseCircleOutline size={25} color="red" />
        </button>
      )}
    </li>
  );
};
