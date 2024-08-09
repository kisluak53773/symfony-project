"use client";

import React, { FC, useEffect, useState } from "react";
import { authService } from "@/services/auth";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { type IRegisterData } from "@/services/auth";
import { type TRegisterNames } from "../../types";
import { REGISTER_FIELDS } from "../../constants";
import Link from "next/link";
import { getAccessToken } from "@/services";
import { useRouter } from "next/navigation";
import { getErrorStatusCode } from "@/services/axios";
import { useAppDispatch } from "@/store";
import { login } from "@/store/slices/user";

export const RegisterForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<IRegisterData>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");
  const isAuthorized = getAccessToken();
  const router = useRouter();
  const dispatch = useAppDispatch();

  useEffect(() => {
    if (isAuthorized) {
      router.replace("/");
    }
  }, [isAuthorized, router]);

  const onSubmit: SubmitHandler<IRegisterData> = async (data) => {
    try {
      reset();
      await authService.register(data);
      dispatch(login());
      setError("");
      router.push("/");
    } catch (error) {
      if (getErrorStatusCode(error) === 400) {
        setError("На этот номер телефона уже зарегестрирован аккаунт");
      } else {
        setError("Что то пошло не так");
      }
    }
  };

  return (
    <form
      className="rounded-lg px-[25px] py-[40px] gap-[20px] bg-gray-100 flex flex-col"
      onSubmit={handleSubmit(onSubmit)}
    >
      {error && (
        <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
          {error}
        </div>
      )}
      {REGISTER_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TRegisterNames}
          rules={fieldData.rules}
          render={({ field, fieldState: { error } }) => (
            <div className="w-full flex flex-col">
              <label
                htmlFor={fieldData.id}
                className={
                  fieldData.rules.required !== undefined
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
              {error && <div className=" text-red-400">{error.message}</div>}
            </div>
          )}
        />
      ))}
      <button
        type="submit"
        className="w-[600px] mt-[20px] text-center bg-blue-500 text-white hover:bg-blue-300 transition-all rounded-[5px] p-[5px]"
      >
        Зарегестрироваться
      </button>
      <div className=" bg-gray-400 h-[1px] w-full mt-[20px] mb-[10px]" />
      <p className=" text-center">
        Уже есть аккаунт?{" "}
        <Link href="/auth" className=" text-blue-500 hover:bg-blue-300">
          Войти в учетную запись
        </Link>
      </p>
    </form>
  );
};
