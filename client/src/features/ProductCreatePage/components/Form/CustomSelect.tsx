"use client";

import React, { FC } from "react";
import Select from "react-select";
import { type ICustomSelectProps } from "../../types";
import { Controller } from "react-hook-form";
import { type IOption } from "@/types";

export const CustomSelect: FC<ICustomSelectProps> = ({
  control,
  options,
  name,
  placeholder,
  requiredMessage,
}) => {
  const getValue = (value: string) =>
    value ? options.find((option) => option.value === value) : "";

  return (
    <Controller
      control={control}
      name={name}
      rules={{
        required: requiredMessage,
      }}
      render={({ field: { onChange, value }, fieldState: { error } }) => (
        <div>
          <Select
            options={options}
            value={getValue(value)}
            placeholder={placeholder}
            onChange={(newValue) => onChange((newValue as IOption).value)}
          />
          {error && <div className=" text-red-400">{error.message}</div>}
        </div>
      )}
    />
  );
};
