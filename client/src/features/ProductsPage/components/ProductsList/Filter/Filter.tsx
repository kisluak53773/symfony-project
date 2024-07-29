"use client";

import React, { FC, useEffect, useRef, useState } from "react";
import { type IFilterProps } from "@/features/ProductsPage/types";
import { IoIosArrowDown } from "react-icons/io";
import { SortList } from "./SortList";
import { type IType, typeService } from "@/services/type";
import { type IProducer, producerService } from "@/services/producer";
import { FilterList } from "./FilterList";

export const Filter: FC<IFilterProps> = ({
  setSort,
  sort,
  producersFilter,
  setProducersFilter,
  typesFilter,
  setTypesFilter,
}) => {
  const [onMouseFilterTypeOver, setOnMouseFilterTypeOver] = useState(false);
  const [onMouseFilterProducerOver, setOnMouseFilterProducerOver] =
    useState(false);
  const [onMouseSortOver, setOnMouseSortOver] = useState(false);
  const [types, setTypes] = useState<IType[]>();
  const [producer, setProducer] = useState<IProducer[]>();
  const filterTypeRef = useRef(null);
  const filterProducerRef = useRef(null);
  const sortRef = useRef(null);

  useEffect(() => {
    (async () => {
      const typesData = await typeService.getTypesForVendor();
      const producerData = await producerService.getProducersForVendor();

      setTypes(typesData);
      setProducer(producerData);
    })();
  }, []);

  return (
    <section className=" py-[40px] flex">
      <div className=" w-full bg-slate-200 flex justify-between">
        <div className=" flex ">
          <h1 className=" text-gray-500 px-[20px] py-[10px]">Фильтр:</h1>
          <p
            ref={filterTypeRef}
            onMouseEnter={() => setOnMouseFilterTypeOver(true)}
            onClick={() => setOnMouseFilterTypeOver(!onMouseFilterTypeOver)}
            className=" flex items-center hover:bg-slate-300 px-[10px] cursor-pointer"
          >
            Тип продукта <IoIosArrowDown className=" ml-[5px]" />
          </p>
          <p
            ref={filterProducerRef}
            onMouseEnter={() => setOnMouseFilterProducerOver(true)}
            onClick={() =>
              setOnMouseFilterProducerOver(!onMouseFilterProducerOver)
            }
            className=" flex items-center hover:bg-slate-300 px-[10px] cursor-pointer"
          >
            Производитель <IoIosArrowDown className=" ml-[5px]" />
          </p>
        </div>
        {onMouseFilterTypeOver && types && (
          <FilterList
            setFilter={setTypesFilter}
            filter={typesFilter}
            filterItemms={types}
            filterRef={filterTypeRef}
            setOnMouseFilterOver={setOnMouseFilterTypeOver}
          />
        )}
        {onMouseFilterProducerOver && producer && (
          <FilterList
            setFilter={setProducersFilter}
            filter={producersFilter}
            filterItemms={producer}
            filterRef={filterProducerRef}
            setOnMouseFilterOver={setOnMouseFilterProducerOver}
          />
        )}
        <div className=" flex">
          <h1 className=" text-gray-500 px-[20px] py-[10px]">Сортировка</h1>
          <p
            ref={sortRef}
            onMouseEnter={() => setOnMouseSortOver(true)}
            onClick={() => setOnMouseSortOver(!onMouseSortOver)}
            className=" flex items-center hover:bg-slate-300 px-[20px] cursor-pointer"
          >
            {sort.title} <IoIosArrowDown className=" ml-[5px] mr-[20px]" />
          </p>
        </div>
        {onMouseSortOver && (
          <SortList
            setOnMouseSortOver={setOnMouseSortOver}
            sortRef={sortRef}
            selectedSort={sort}
            setSort={setSort}
          />
        )}
      </div>
    </section>
  );
};
