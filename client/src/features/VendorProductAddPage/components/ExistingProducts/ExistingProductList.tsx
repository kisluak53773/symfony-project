"use client";

import React, { FC, useEffect, useState } from "react";
import {
  productService,
  type IProductVendorDoesNotSell,
} from "@/services/product";
import { ExistingProductItem } from "./ExistingProductItem";
import { CustomPagination } from "@/components/Pagination";

export const ExistingProductList: FC = () => {
  const [products, setProducts] = useState<IProductVendorDoesNotSell[] | null>(
    null
  );
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [refetch, setRefetch] = useState(false);

  const handleRefetch = () => {
    if (products && products.length === 1 && page !== 1) {
      setPage(page - 1);
    } else {
      setRefetch(!refetch);
    }
  };

  useEffect(() => {
    (async () => {
      try {
        const data = await productService.getProductsVendorDoesNotSell(page);

        setProducts(data.data);
        setTotalPages(data.total_pages);
      } catch (error) {
        setProducts(null);
        setTotalPages(1);
      }
    })();
  }, [page]);

  return (
    <>
      {products && products.length > 0 ? (
        <>
          <ul>
            {products.map((item) => (
              <ExistingProductItem
                handleRefetch={handleRefetch}
                key={item.id}
                product={item}
              />
            ))}
          </ul>
          <CustomPagination
            currentPage={page}
            setPage={setPage}
            totalPageCount={totalPages}
          />
        </>
      ) : (
        <h1 className="text-[26px] flex items-center justify-center">
          Таких продуктов нету
        </h1>
      )}
    </>
  );
};
