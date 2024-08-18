"use client";

import React, { FC, useEffect, useState } from "react";
import { useSearchParams } from "next/navigation";
import { useDebounce } from "@/hooks";
import { productService, type IProduct } from "@/services/product";
import { ProductItem } from "@/components/ProductItem";
import { CustomPagination } from "@/components/Pagination";
import { HeaderImage } from "@/components/Header/HeaderImage";
import { Filter } from "./Filter";
import { SORT_TYPES } from "../../constants";
import { type ISort } from "../../types";

export const ProductsList: FC = () => {
  const params = useSearchParams();
  const [search, setSearch] = useState<string>(
    params.has("search") ? (params.get("search") as string) : ""
  );
  const debouncedSearch = useDebounce<string>(search);
  const [products, setProducts] = useState<IProduct[] | null>(null);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [sort, setSort] = useState<ISort>(SORT_TYPES[0]);
  const [types, setTypes] = useState<number[]>([]);
  const [producers, setProducers] = useState<number[]>([]);

  const handleProductCahange = (product: IProduct) => {
    if (products) {
      setProducts(
        products.map((item) => (item.id === product.id ? product : item))
      );
    }
  };

  useEffect(() => {
    if (debouncedSearch) {
      (async () => {
        const data = await productService.getProducts({
          title: debouncedSearch,
          limit: 6,
          page: page,
          types: types,
          producers: producers,
          sort,
        });

        setProducts(data.data);
        setTotalPages(data.total_pages);
      })();
    } else {
      setProducts(null);
    }
  }, [debouncedSearch, page, types, producers, sort]);

  return (
    <section className=" flex flex-col py-[20px]">
      <Filter
        producersFilter={producers}
        setProducersFilter={setProducers}
        typesFilter={types}
        setTypesFilter={setTypes}
        sort={sort}
        setSort={setSort}
      />
      <div className=" rounded-lg p-[10px] bg-gray-100 w-full flex gap-[5px]">
        <HeaderImage type="search" />
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          type="text"
          className=" bg-gray-100 w-full focus:outline-none"
        />
      </div>
      {products ? (
        <>
          <ul className=" grid grid-cols-3">
            {products.map((item) => (
              <li className=" pr-[10px]" key={item.id}>
                <ProductItem
                  handleProductCahange={handleProductCahange}
                  product={item}
                />
              </li>
            ))}
          </ul>
          <CustomPagination
            setPage={setPage}
            totalPageCount={totalPages}
            currentPage={page}
          />
        </>
      ) : (
        <div className=" h-[80vh] flex items-center justify-center">
          <h1 className=" font-semibold text-[18px]">
            По вашему запросу результатов нет
          </h1>
        </div>
      )}
    </section>
  );
};
