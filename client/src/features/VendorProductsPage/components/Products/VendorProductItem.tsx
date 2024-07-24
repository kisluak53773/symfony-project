"use client";

import React, { FC, useState } from "react";
import { type IProductVendorItemProps } from "../../types";
import { productImagePathConverter } from "@/services";
import { Modal } from "@/components/Modal";
import { VendorProductModal } from "./VendorProductModal";
import { FaRegTrashCan } from "react-icons/fa6";
import { productService } from "@/services/product";

export const VendorProductItem: FC<IProductVendorItemProps> = ({
  vendorProduct,
  handleRefetch,
  handleProductOfVendorUpdate,
}) => {
  const [isModelActive, setIsModelActive] = useState(false);

  const handleDelete = async () => {
    await productService.deleteProductForVendor(vendorProduct.id);
    handleRefetch();
  };

  return (
    <li className=" mb-[10px] group/vendorProduct">
      <section className=" flex relative items-center rounded-lg border-[1px] border-gray-300 border-solid px-[20px] py-[10px]">
        <img
          src={productImagePathConverter(vendorProduct.product.image)}
          width={400}
          height={200}
          className="w-[150px] h-[150px] mr-[10px]"
        />
        <div className=" flex flex-col">
          <h1>{vendorProduct.product.title}</h1>
          <div className="flex gap-[20px]">
            <span>Еденица измерения: {vendorProduct.product.weight}</span>
            <span>Ваша цена: {vendorProduct.price} руб. за еденицу товара</span>
            <span>На складе {vendorProduct.quantity} шт.</span>
          </div>
        </div>
        <button
          onClick={() => setIsModelActive(!isModelActive)}
          className=" text-center rounded-lg ml-auto px-[20px] py-[10px] text-white bg-button hover:bg-buttonHover"
        >
          Редактировать
        </button>
        <button
          onClick={handleDelete}
          className="absolute right-[10px] top-[10px] hidden group-hover/vendorProduct:block"
        >
          <FaRegTrashCan size={25} color="red" />
        </button>
      </section>
      {isModelActive && (
        <Modal
          setIsModelActive={setIsModelActive}
          classes={{
            modalWindow: "absolute ml-[32vw]",
          }}
        >
          <VendorProductModal
            handleProductOfVendorUpdate={handleProductOfVendorUpdate}
            setIsModelActive={setIsModelActive}
            vendorProduct={vendorProduct}
          />
        </Modal>
      )}
    </li>
  );
};
