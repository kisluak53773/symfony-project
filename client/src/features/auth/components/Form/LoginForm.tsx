"use client";

import React, { FC, useEffect, useState } from "react";
import { authService } from "@/services/auth";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { type ILoginData } from "@/services/auth";
import { LOGIN_FIELDS } from "../../constants";
import { type TLoginNames } from "../../types";
import Link from "next/link";
import { getAccessToken } from "@/services";
import { useRouter } from "next/navigation";
import { getErrorStatusCode } from "@/services/axios";

export const LoginForm: FC = () => {
  const { control, handleSubmit, reset } = useForm<ILoginData>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");
  const isAuthorized = getAccessToken();
  const router = useRouter();

  useEffect(() => {
    if (isAuthorized) {
      router.replace("/");
    }
  }, [isAuthorized, router]);

  const onSubmit: SubmitHandler<ILoginData> = async (data) => {
    try {
      reset();
      await authService.login(data);
      setError("");
      router.push("/");
    } catch (error) {
      if (getErrorStatusCode(error) === 400) {
        setError("Неправильный пароль или номер телефона");
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
      {LOGIN_FIELDS.map((fieldData) => (
        <Controller
          key={fieldData.id}
          control={control}
          name={fieldData.name as TLoginNames}
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
        Войти
      </button>
      <div className=" bg-gray-400 h-[1px] w-full mt-[20px] mb-[10px]" />
      <p className=" text-center">
        Новый пользователь?{" "}
        <Link
          href="/auth?type=register"
          className=" text-blue-500 hover:marker:bg-blue-300"
        >
          Создать учетную запись
        </Link>
      </p>
    </form>
  );
};
