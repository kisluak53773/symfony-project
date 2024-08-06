"use client";

import React, { FC, useEffect, useState } from "react";
import { type IRequestDetailsProps } from "../../types";
import {
  type IOrder,
  orderService,
  type ISpecificRequestData,
} from "@/services/order";
import { OrderDetails } from "./OrderDetails";
import { RequestProductList } from "./RequestProductList";

export const RequestDetails: FC<IRequestDetailsProps> = ({ orderId }) => {
  const [request, setRequest] = useState<ISpecificRequestData | null>(null);

  const handleOrderUpdate = (order: IOrder) => {
    if (request) {
      setRequest({ ...request, orderData: order });
    }
  };

  useEffect(() => {
    (async () => {
      const data = await orderService.getOrderRequestByOrderId(orderId);

      const totalPrice = data.products.reduce(
        (acc, item) => acc + parseFloat(item.price) * item.quantity,
        0
      );
      setRequest({
        products: [...data.products],
        orderData: { ...data.orderData, totalPrice: totalPrice },
      });
    })();
  }, [orderId]);

  return (
    <>
      {request && (
        <>
          <OrderDetails order={request.orderData} />
          <RequestProductList
            products={request.products}
            handleOrderUpdate={handleOrderUpdate}
            order={request.orderData}
          />
        </>
      )}
    </>
  );
};
