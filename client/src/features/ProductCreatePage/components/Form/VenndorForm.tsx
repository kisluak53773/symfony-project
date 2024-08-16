"use client";

import React, { FC, useEffect, useState } from "react";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { PRODUCT_VENDOR_FORM_FIELDS } from "../../constants/formConstants";
import { getErrorStatusCode } from "@/services/axios";
import {
  type IProductVendorCreate,
  type TProductVendorNames,
} from "../../types";
import { productService } from "@/services/product";
import { convertToFormData } from "@/services";
import { typeService } from "@/services/type";
import { producerService } from "@/services/producer";
import { convertToReactSelectOptions } from "@/services";
import { IOption } from "@/types";
import { CustomSelect } from "./CustomSelect";

export const VenndorForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<IProductVendorCreate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");
  const [img, setImg] = useState<File | null>(null);
  const [types, setTypes] = useState<IOption[]>();
  const [producers, setProducers] = useState<IOption[]>();

  useEffect(() => {
    (async () => {
      const producersData = await producerService.getProducersForVendor();
      const typesData = await typeService.getTypesForVendor();

      setTypes(convertToReactSelectOptions(typesData));
      setProducers(convertToReactSelectOptions(producersData));
    })();
  }, []);

  const onSubmit: SubmitHandler<IProductVendorCreate> = async (data) => {
    try {
      if (img !== null) {
        const formData = convertToFormData(data);
        formData.append("image", img);
        await productService.creteProduct(formData);
        reset();
        setImg(null);
        setError("");
      } else {
        setError("Картинка для продукта обязательна");
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
      className="rounded-lg px-[25px] py-[40px] gap-[20px] w-full bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {PRODUCT_VENDOR_FORM_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TProductVendorNames}
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
                  onChange={(e) => {
                    const value = e.target.value;
                    field.onChange(
                      fieldData.valueType === "number" ? parseInt(value) : value
                    );
                  }}
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
        />
      </div>
      {types && producers && (
        <>
          <CustomSelect
            name="typeId"
            placeholder="Выберите тип продукта"
            control={control}
            options={types}
            requiredMessage="Тип обязателен для выбора"
          />
          <CustomSelect
            name="producerId"
            placeholder="Выберите производителя для продукта"
            control={control}
            options={producers}
            requiredMessage="Производитель обязателен для выбора"
          />
        </>
      )}
      <button
        type="submit"
        className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Создать производителя
      </button>
    </form>
  );
};
