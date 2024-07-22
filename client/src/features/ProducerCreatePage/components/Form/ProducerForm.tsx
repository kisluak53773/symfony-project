"use client";

import React, { FC, useState } from "react";
import { producerService, type IProducerCreate } from "@/services/producer";
import { PRODUCER_FORM_FIELDS } from "../../constants";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { getErrorStatusCode } from "@/services/axios";
import { type TProducerNames } from "../../types";

export const ProducerForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<IProducerCreate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");

  const onSubmit: SubmitHandler<IProducerCreate> = async (data) => {
    try {
      reset();
      await producerService.createProducer(data);
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
      className="rounded-lg px-[25px] items-center py-[40px] w-[80%] gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {PRODUCER_FORM_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TProducerNames}
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
        className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Создать производителя
      </button>
    </form>
  );
};
