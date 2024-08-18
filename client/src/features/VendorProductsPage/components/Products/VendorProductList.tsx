"use client";

import React, { FC, useEffect, useState } from "react";
import { type IProductOfVendor, productService } from "@/services/product";
import { VendorProductItem } from "./VendorProductItem";
import { CustomPagination } from "@/components/Pagination";
import { type IVendorProductForm } from "../../types";

export const VendorProductList: FC = () => {
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [products, setProducsts] = useState<IProductOfVendor[] | null>(null);
  const [refetch, setRefetch] = useState(false);

  const handleRefetch = () => {
    if (products && products.length === 1 && page !== 1) {
      setPage(page - 1);
    } else {
      setRefetch(!refetch);
    }
  };

  const handleProductOfVendorUpdate = (
    data: IVendorProductForm,
    id: number
  ) => {
    const vendorProduct = products?.find((item) => item.id === id);
    const newVendor = { ...vendorProduct, ...data };

    setProducsts(
      products?.map((item) =>
        item.id === newVendor.id ? newVendor : item
      ) as IProductOfVendor[]
    );
  };

  useEffect(() => {
    (async () => {
      try {
        const data = await productService.getVendorProducts(page);

        setTotalPages(data.total_pages);
        setProducsts(data.data);
      } catch (error) {
        setProducsts(null);
        setTotalPages(1);
      }
    })();
  }, [page, refetch]);

  return (
    <>
      {products && products.length > 0 ? (
        <section>
          <ul>
            {products.map((item) => (
              <VendorProductItem
                handleProductOfVendorUpdate={handleProductOfVendorUpdate}
                handleRefetch={handleRefetch}
                key={item.id}
                vendorProduct={item}
              />
            ))}
          </ul>
          <CustomPagination
            totalPageCount={totalPages}
            currentPage={page}
            setPage={setPage}
          />
        </section>
      ) : (
        <section className=" flex items-center justify-center">
          <h1 className="text-[26px]">Вы еще ничего не продаете</h1>
        </section>
      )}
    </>
  );
};
