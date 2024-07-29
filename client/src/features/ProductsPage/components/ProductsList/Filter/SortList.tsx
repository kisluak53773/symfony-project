"use client";

import React, { FC } from "react";
import { type ISortListProps, type ISort } from "@/features/ProductsPage/types";
import { SORT_TYPES } from "@/features/ProductsPage/constants";

export const SortList: FC<ISortListProps> = ({
  selectedSort,
  setSort,
  sortRef,
  setOnMouseSortOver,
}) => {
  const handleClick = (item: ISort) => {
    setSort(item);
    setOnMouseSortOver(false);
  };

  return (
    <ul
      onMouseLeave={() => setOnMouseSortOver(false)}
      className="absolute z-10 bg-slate-100 py-[5px]"
      style={{
        marginTop: `${sortRef.current?.getBoundingClientRect().height}px`,
        left: `${sortRef.current?.getBoundingClientRect().left}px`,
        width: `${
          (sortRef.current?.getBoundingClientRect().right as number) -
          (sortRef.current?.getBoundingClientRect().left as number)
        }px`,
      }}
    >
      {SORT_TYPES.map((item) => (
        <li key={item.id}>
          <button
            className={
              selectedSort.id === item.id
                ? "bg-slate-200 w-full pl-[7px] text-left h-full"
                : "w-full h-full text-left pl-[7px]"
            }
            onClick={() => handleClick(item)}
          >
            {item.title}
          </button>
        </li>
      ))}
    </ul>
  );
};
