"use client";

import React, { FC, useState } from "react";
import { type IExistingProductItemProps } from "../../types";
import { productImagePathConverter } from "@/services";
import { ExistingProductModal } from "./ExistingProductModal";
import { Modal } from "@/components/Modal";

export const ExistingProductItem: FC<IExistingProductItemProps> = ({
  product,
  handleRefetch,
}) => {
  const [isModalActive, setIsModalActive] = useState(false);

  return (
    <li className=" mb-[10px]">
      <section className=" flex relative items-center rounded-lg border-[1px] border-gray-300 border-solid px-[20px] py-[10px]">
        <img
          src={productImagePathConverter(product.image)}
          width={400}
          height={200}
          className="w-[150px] h-[150px] mr-[10px]"
        />
        <div className=" flex flex-col gap-[10px]">
          <h1 className=" font-semibold">{product.title}</h1>
          <div className="flex gap-[5px]">
            <span>Тип товара: {product.type.title}</span>
            <span>Производитель: {product.producer.title}</span>
            <span>Страна происхождения: {product.producer.country}</span>
          </div>
        </div>
        <button
          onClick={() => setIsModalActive(!isModalActive)}
          className=" text-center rounded-lg ml-auto px-[10px] py-[5px] bg-button text-white hover:bg-buttonHover"
        >
          Добавить в каталог
        </button>
      </section>
      {isModalActive && (
        <Modal
          setIsModelActive={setIsModalActive}
          classes={{
            modalWindow: "absolute ml-[32vw]",
          }}
        >
          <ExistingProductModal
            setIsModalActive={setIsModalActive}
            productId={product.id}
            handleRefetch={handleRefetch}
          />
        </Modal>
      )}
    </li>
  );
};
