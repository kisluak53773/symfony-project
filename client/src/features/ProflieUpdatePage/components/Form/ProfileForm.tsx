"use client";

import React, { FC, useEffect, useState } from "react";
import { SubmitHandler, useForm, Controller } from "react-hook-form";
import { type IProfileUpdate, type IUser, userService } from "@/services/user";
import { getErrorStatusCode } from "@/services/axios";
import { PROFILE_FIELDS_TO_UPDATE } from "../../constants";
import { type TProfileUpdateNames } from "../../types";

export const ProfileForm: FC = () => {
  const [user, setUser] = useState<IUser>();
  const { control, handleSubmit, reset } = useForm<IProfileUpdate>({
    mode: "onBlur",
  });
  const [error, setError] = useState("");

  const onSubmit: SubmitHandler<IProfileUpdate> = async (data) => {
    try {
      await userService.updateProfile(data);
      setError("");
      setUser({ ...(user as IUser), ...data });
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
      const data = await userService.getCurrentUser();
      setUser(data);
    })();
  }, []);

  return (
    <>
      {user && (
        <form
          className="rounded-lg px-[25px] py-[40px] gap-[20px] bg-gray-100 flex flex-col"
          onSubmit={handleSubmit(onSubmit)}
        >
          {error && (
            <div className=" bg-error p-[10px] text-red-400 rounded-[10px] text-center mt-[5px]">
              {error}
            </div>
          )}
          {PROFILE_FIELDS_TO_UPDATE.map((fieldData) => (
            <Controller
              key={fieldData.id}
              control={control}
              name={fieldData.name as TProfileUpdateNames}
              defaultValue={
                user && (fieldData.name as keyof IUser) in user
                  ? user[fieldData.name as keyof IUser]
                    ? String(user[fieldData.name as keyof IUser])
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
