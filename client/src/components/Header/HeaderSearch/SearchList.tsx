"use client";

import React, { FC, useEffect, useState } from "react";
import { type ISearchListProsp } from "../@types";
import { productService, type IProduct } from "@/services/product";
import { useDebounce } from "@/hooks";
import { SearchItem } from "./SearchItem";
import Link from "next/link";

export const SearchList: FC<ISearchListProsp> = ({ search }) => {
  const [products, setProducts] = useState<IProduct[] | null>(null);
  const debouncedSearch = useDebounce<string>(search);
  const [quantity, setQunatity] = useState(0);

  useEffect(() => {
    if (debouncedSearch) {
      (async () => {
        const data = await productService.getProducts({
          title: debouncedSearch,
          limit: 4,
        });

        setProducts(data.data);
        setQunatity(data.total_items);
      })();
    } else {
      setProducts(null);
      setQunatity(0);
    }
  }, [debouncedSearch]);

  return (
    <>
      {products && (
        <div className=" bg-white border-[1px] absolute top-[8vh] left-[35vw] w-full border-gray-400 rounded-b-lg">
          <ul className=" max-h-[600px] overflow-auto">
            {products.map((item) => (
              <SearchItem key={item.id} product={item} />
            ))}
          </ul>
          {quantity > 4 && (
            <div className="flex items-center justify-center w-full py-[10px]">
              <Link href={`/producst?search=${debouncedSearch}`}>
                Показать все продукты
              </Link>
            </div>
          )}
        </div>
      )}
    </>
  );
};
