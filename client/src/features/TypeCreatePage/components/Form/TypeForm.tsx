"use client";

import React, { FC, useState } from "react";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { getErrorStatusCode } from "@/services/axios";
import { type TTypeNames, type ITypeCreate } from "../../types/@types";
import { typeService } from "@/services/type";
import { convertToFormData } from "@/services";
import { TYPE_FORM_FIELDS } from "../../constants";

export const TypeForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<ITypeCreate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");
  const [img, setImg] = useState<File | null>(null);

  const onSubmit: SubmitHandler<ITypeCreate> = async (data) => {
    try {
      if (img) {
        const formData = convertToFormData(data);
        formData.append("image", img);
        await typeService.createaType(formData);
        reset();
        setImg(null);
        setError("");
      } else {
        setError("Картинка типа продукта обязательна");
      }
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
      className="rounded-lg px-[25px] py-[40px] w-[80%] items-center gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {TYPE_FORM_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TTypeNames}
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
      <div className="flex gap-[15px] items-center">
        <label
          htmlFor="imgInput"
          className=" text-white bg-button hover:bg-buttonHover rounded-xl px-[20px] py-[10px] cursor-pointer"
        >
          Выбрать фото для типа продукта
        </label>
        {img && (
          <img className="w-[150px] h-[100px]" src={URL.createObjectURL(img)} />
        )}
        <input
          multiple={false}
          onChange={(e) => {
            const file = e.target.files && e.target.files[0];
            if (file) {
              setImg(file);
            }
          }}
          id="imgInput"
          className=" hidden"
          type="file"
          required
        />
      </div>
      <button
        type="submit"
        className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Создать тип продукта
      </button>
    </form>
  );
};
