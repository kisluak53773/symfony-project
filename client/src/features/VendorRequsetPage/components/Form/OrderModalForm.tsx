"use client";

import React, { FC, useState } from "react";
import { EDIT_ORDER_CONSTANTS } from "../../constants";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { getErrorStatusCode } from "@/services/axios";
import {
  type IOrderModalFormProps,
  type IOrderEditFields,
  type TOrderEdit,
} from "../../types";
import { orderService, type IOrder } from "@/services/order";

export const OrderModalForm: FC<IOrderModalFormProps> = ({
  order,
  setModalActive,
  handleOrderUpdate,
}) => {
  const { control, handleSubmit } = useForm<IOrderEditFields>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");

  const onSubmit: SubmitHandler<IOrderEditFields> = async (data) => {
    try {
      const newOrder = { ...order, ...data };
      await orderService.patchOrder(newOrder);
      setError("");
      handleOrderUpdate(newOrder);
      setModalActive(false);
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
      className="rounded-lg px-[25px] mt-[10px] items-center py-[40px] w-[40vw] gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {EDIT_ORDER_CONSTANTS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TOrderEdit}
          rules={fieldData.rules}
          defaultValue={
            order && (fieldData.name as keyof IOrder) in order
              ? order[fieldData.name as keyof IOrder]
                ? String(order[fieldData.name as keyof IOrder])
                : ""
              : ""
          }
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
      <button
        type="submit"
        className="w-[35vw] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Сохранить изменения
      </button>
    </form>
  );
};
