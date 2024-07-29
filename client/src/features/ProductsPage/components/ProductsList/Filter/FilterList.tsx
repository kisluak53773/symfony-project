"use client";

import React, { FC } from "react";
import { type IFilterListProps } from "@/features/ProductsPage/types";

export const FilterList: FC<IFilterListProps> = ({
  filter,
  setFilter,
  filterRef,
  setOnMouseFilterOver,
  filterItemms,
}) => {
  const handleClick = (id: number) => {
    if ([...filter].find((item) => item === id)) {
      setFilter([...filter].filter((item) => item !== id));
    } else {
      setFilter([...filter, id]);
    }
  };

  console.log(filter);

  return (
    <ul
      onMouseLeave={() => setOnMouseFilterOver(false)}
      className="absolute z-10 bg-slate-100 py-[5px]"
      style={{
        marginTop: `${filterRef.current?.getBoundingClientRect().height}px`,
        left: `${filterRef.current?.getBoundingClientRect().left}px`,
        width: `${
          (filterRef.current?.getBoundingClientRect().right as number) -
          (filterRef.current?.getBoundingClientRect().left as number)
        }px`,
      }}
    >
      {filterItemms.map((item) => (
        <li key={item.id}>
          <input
            className=" ml-[7px]"
            type="checkbox"
            checked={!!filter.find((filterId) => filterId == item.id)}
            onChange={() => handleClick(item.id)}
            name={item.title}
            id={item.id.toString()}
          />
          <label className=" pl-[7px] text-center" htmlFor={item.id.toString()}>
            {item.title}
          </label>
        </li>
      ))}
    </ul>
  );
};
