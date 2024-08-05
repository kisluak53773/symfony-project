"use client";

import React, { FC, useState } from "react";
import { type TOrderNames } from "../../types";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { getErrorStatusCode } from "@/services/axios";
import {
  ORDER_FORM_CONSTANTS,
  PAYMENY_METHODS_FORM_CONSTANTS,
} from "../../constants";
import { orderService, type IOrderCreate } from "@/services/order";
import { useAppDispatch } from "@/store";
import { emptyCart } from "@/store/slices/cart";

export const OrderForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<IOrderCreate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");
  const dispatch = useAppDispatch();

  const onSubmit: SubmitHandler<IOrderCreate> = async (data) => {
    try {
      reset();
      await orderService.createOrder(data);
      dispatch(emptyCart());
      setError("");
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
      className="rounded-lg px-[25px] mt-[10px] items-center py-[40px] w-full gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {ORDER_FORM_CONSTANTS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TOrderNames}
          rules={fieldData.rules}
          render={({ field, fieldState: { error } }) => {
            return fieldData.type !== "textarea" ? (
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
            ) : (
              <div className="w-full flex flex-col">
                <label htmlFor={fieldData.id}>{fieldData.title}</label>
                <textarea
                  id={fieldData.id}
                  {...field}
                  placeholder={fieldData.placeholder}
                  className=" focus:outline-none h-[35px] rounded-sm px-[5px]"
                />
                {error && <div className=" text-red-400">{error.message}</div>}
              </div>
            );
          }}
        />
      ))}
      <div className="w-full">
        <h1 className=" font-medium">Способ оплаты</h1>
      </div>
      {PAYMENY_METHODS_FORM_CONSTANTS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TOrderNames}
          rules={fieldData.rules}
          render={({ field, fieldState: { error } }) => (
            <div className=" w-full flex gap-[10px]">
              <input
                id={fieldData.id}
                type={fieldData.type}
                {...field}
                placeholder={fieldData.placeholder}
                value={fieldData.value}
              />
              <label htmlFor={fieldData.id}>{fieldData.title}</label>
              {error && <div className=" text-red-400">{error.message}</div>}
            </div>
          )}
        />
      ))}
      <button
        type="submit"
        className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Оформить заказ
      </button>
    </form>
  );
};
