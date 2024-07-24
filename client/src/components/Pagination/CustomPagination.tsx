"use client";

import React, { FC } from "react";
import { type IPaginationProps } from "./@types";
import { usePagination } from "@/hooks";
import { DOTS } from "@/constants";
import { MdOutlineKeyboardArrowLeft } from "react-icons/md";
import { MdOutlineKeyboardArrowRight } from "react-icons/md";

export const CustomPagination: FC<IPaginationProps> = ({
  totalPageCount,
  siblingCount = 1,
  currentPage,
  setPage,
}) => {
  const paginationRange = usePagination({
    totalPageCount,
    currentPage,
    siblingCount,
  });

  const handlePrevios = () => {
    if (currentPage > 1) {
      setPage(currentPage - 1);
    }
  };

  const handleNext = () => {
    if (currentPage !== totalPageCount) {
      setPage(currentPage + 1);
    }
  };

  if (currentPage === 0 || paginationRange.length < 2) {
    return null;
  }

  return (
    <ul className=" flex gap-[5px] w-full justify-center">
      <li className=" flex items-center justify-center">
        <button onClick={handlePrevios}>
          <MdOutlineKeyboardArrowLeft size={25} />
        </button>
      </li>
      {paginationRange.map((pageNumber, index) => {
        if (pageNumber === DOTS) {
          return <li key={index}>&#8230;</li>;
        }

        return (
          <li key={index}>
            <button
              className={
                (pageNumber as number) === currentPage
                  ? " bg-button py-[5px] px-[10px]"
                  : " hover:text-gray-400 py-[5px] px-[10px]"
              }
              onClick={() => setPage(pageNumber as number)}
            >
              {pageNumber}
            </button>
          </li>
        );
      })}
      <li className=" flex items-center justify-center">
        <button onClick={handleNext}>
          <MdOutlineKeyboardArrowRight size={25} />
        </button>
      </li>
    </ul>
  );
};
