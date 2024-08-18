"use client";

import React, { FC, useEffect, useState } from "react";
import { orderService } from "@/services/order";
import { type IOrder } from "@/services/order";
import { OrderItem } from "./OrderItem";
import { CustomPagination } from "@/components/Pagination";

export const OrdersList: FC = () => {
  const [orders, setOrders] = useState<IOrder[] | null>(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const handleOrderUpdate = (order: IOrder) => {
    setOrders(
      orders?.map((item) => (item.id === order.id ? order : item)) as IOrder[]
    );
  };

  useEffect(() => {
    (async () => {
      const data = await orderService.getOrdersOfCurrentUser({ page });

      setOrders(data.data);
      setTotalPages(data.total_pages);
    })();
  }, [page]);

  return (
    <section>
      <h1 className=" font-semibold text-[20px]">Ваши заказы</h1>
      {orders && orders.length > 0 ? (
        <>
          <ul className=" mt-[20px] grid grid-cols-2">
            {orders.map((item) => (
              <OrderItem
                key={item.id}
                order={item}
                handleOrderUpdate={handleOrderUpdate}
              />
            ))}
          </ul>
          <CustomPagination
            totalPageCount={totalPages}
            currentPage={page}
            setPage={setPage}
          />
        </>
      ) : (
        <div className="w-full h-[80vh] flex flex-col items-center justify-center">
          <h1 className=" text-[20px] font-medium">
            Вы еще не заказывали товары
          </h1>
          <p className="text-[18px]">
            Ищите товары в каталоге и поиске, смотрите интересные подборки на
            главной
          </p>
        </div>
      )}
    </section>
  );
};
