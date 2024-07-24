"use client";

import React, { FC, useState } from "react";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { VENDOR_PRODUCT_FIELDS } from "@/features/VendorProductsPage";
import {
  type TVendorProductNames,
  type IVendorProductForm,
} from "@/features/VendorProductsPage/types";
import { getErrorStatusCode } from "@/services/axios";
import { type IExistingProductModalProps } from "../../types";
import { productService } from "@/services/product";

export const ExistingProductModal: FC<IExistingProductModalProps> = ({
  productId,
  setIsModalActive,
  handleRefetch,
}) => {
  const { control, handleSubmit, reset } = useForm<IVendorProductForm>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");

  const onSubmit: SubmitHandler<IVendorProductForm> = async (data) => {
    try {
      await productService.setProductFroVendor({ ...data, productId });
      setError("");
      reset();
      handleRefetch();
      setIsModalActive(false);
    } catch (error) {
      if (getErrorStatusCode(error) === 400) {
        setError("Вы ввели не все данные");
      } else {
        setError("Что то пошло не так");
      }
    }
  };

  return (
    <form
      className="rounded-lg px-[25px] items-center py-[40px] w-[80%] gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {VENDOR_PRODUCT_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TVendorProductNames}
          rules={fieldData.rules}
          render={({ field, fieldState: { error } }) => (
            <div className="w-full flex flex-col">
              <label htmlFor={fieldData.id}>{fieldData.title}</label>
              <input
                id={fieldData.id}
                type={fieldData.type}
                {...field}
                placeholder={fieldData.placeholder}
                className=" focus:outline-none h-[35px] rounded-sm px-[5px]"
              />
              {error && <div className=" text-red-400">{error.message}</div>}
            </div>
          )}
        />
      ))}
      <button
        type="submit"
        className="w-[300px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Начать продавать
      </button>
    </form>
  );
};
