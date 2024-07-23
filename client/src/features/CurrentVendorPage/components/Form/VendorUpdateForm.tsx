"use client";

import React, { FC, useEffect, useState } from "react";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import {
  type IVendor,
  type IVendorToUpdate,
  vendorService,
} from "@/services/vendor";
import { getErrorStatusCode } from "@/services/axios";
import { VENDOR_FIELDS_TO_UPDATE } from "../../constants";
import { type TVendorUpdateNames } from "../../types";

export const VendorUpdateForm: FC = () => {
  const [vendor, setVendor] = useState<IVendor>();
  const { control, handleSubmit, reset } = useForm<IVendorToUpdate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");

  const onSubmit: SubmitHandler<IVendorToUpdate> = async (data) => {
    try {
      await vendorService.updateCurrentVendor(data);
      setError("");
      setVendor({ ...(vendor as IVendor), ...data });
    } catch (error) {
      if (getErrorStatusCode(error) === 400) {
        setError("На этот номер телефона уже зарегестрирован аккаунт");
      } else {
        setError("Что то пошло не так");
      }
    }
  };

  useEffect(() => {
    (async () => {
      const data = await vendorService.getCurrentVendor();
      setVendor(data);
    })();
  }, []);

  return (
    <>
      {vendor && (
        <form
          className="rounded-lg px-[25px] py-[40px] gap-[20px] bg-gray-100 flex flex-col"
          onSubmit={handleSubmit(onSubmit)}
        >
          {error && (
            <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
              {error}
            </div>
          )}
          {VENDOR_FIELDS_TO_UPDATE.map((fieldData) => (
            <Controller
              key={fieldData.id}
              control={control}
              name={fieldData.name as TVendorUpdateNames}
              defaultValue={
                vendor && (fieldData.name as keyof IVendor) in vendor
                  ? vendor[fieldData.name as keyof IVendor]
                    ? String(vendor[fieldData.name as keyof IVendor])
                    : ""
                  : ""
              }
              rules={fieldData.rules}
              render={({ field, fieldState: { error } }) => (
                <div className="w-full flex flex-col">
                  <label
                    htmlFor={fieldData.id}
                    className={
                      fieldData.rules.required
                        ? "after:content-['*'] after:ml-0.5 after:text-red-500"
                        : ""
                    }
                  >
                    {fieldData.title}
                  </label>
                  <input
                    id={fieldData.id}
                    type={fieldData.type}
                    {...field}
                    placeholder={fieldData.placeholder}
                    className=" focus:outline-none h-[35px] rounded-sm px-[5px]"
                  />
                  {error && (
                    <div className=" text-red-400">{error.message}</div>
                  )}
                </div>
              )}
            />
          ))}
          <button
            type="submit"
            className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
          >
            Сохранить изменения
          </button>
        </form>
      )}
    </>
  );
};
