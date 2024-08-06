"use client";

import React, { FC, useState } from "react";
import { type IRequestProductListProps } from "../../../types";
import { RequsetProductItem } from "./RequsetProductItem";
import { Modal } from "@/components/Modal";
import { OrderModalForm } from "../../Form";

export const RequestProductList: FC<IRequestProductListProps> = ({
  products,
  order,
  handleOrderUpdate,
}) => {
  const [isModalActive, setModalActive] = useState(false);

  return (
    <article className=" mt-[20px]">
      <div className=" flex justify-between">
        <h1 className=" font-semibold text-[20px]">
          Продукты, которые у вас заказали
        </h1>
        <button
          onClick={() => setModalActive(!isModalActive)}
          className=" border-[1px] border-solid border-blue-500 text-blue-500 hover:text-white hover:bg-blue-500 rounded-lg px-[10px] py-[5px]"
        >
          Редактировать заказ
        </button>
      </div>
      <ul className=" mt-[20px]">
        {products.map((item) => (
          <RequsetProductItem key={item.id} product={item} />
        ))}
      </ul>
      {isModalActive && (
        <Modal
          setIsModelActive={setModalActive}
          classes={{
            modalWindow: "absolute ml-[32vw]",
          }}
        >
          <OrderModalForm
            order={order}
            setModalActive={setModalActive}
            handleOrderUpdate={handleOrderUpdate}
          />
        </Modal>
      )}
    </article>
  );
};
