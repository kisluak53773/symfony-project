"use client";

import React, { FC, useEffect, useState } from "react";
import { orderService } from "@/services/order";
import { type IOrder } from "@/services/order";
import { RequestItem } from "./RequestItem";
import { CustomPagination } from "@/components/Pagination";

export const RequestList: FC = () => {
  const [orders, setOrders] = useState<IOrder[] | null>(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  useEffect(() => {
    (async () => {
      const data = await orderService.getVendorOrderRequsets({ page });

      setOrders(data.data);
      setTotalPages(data.total_pages);
    })();
  }, [page]);

  return (
    <>
      {orders && orders.length > 0 ? (
        <section>
          <ul className=" mt-[20px] grid grid-cols-2">
            {orders.map((item) => (
              <RequestItem key={item.id} order={item} />
            ))}
          </ul>
          <CustomPagination
            totalPageCount={totalPages}
            currentPage={page}
            setPage={setPage}
          />
        </section>
      ) : (
        <section className="w-full h-[80vh] flex flex-col items-center justify-center">
          <h2 className=" text-[20px] font-medium">
            К вам еще не поступали заявки
          </h2>
        </section>
      )}
    </>
  );
};
