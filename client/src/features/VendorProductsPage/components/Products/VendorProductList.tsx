"use client";

import React, { FC, useEffect, useState } from "react";
import { type IProductOfVendor, productService } from "@/services/product";
import { VendorProductItem } from "./VendorProductItem";

export const VendorProductList: FC = () => {
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [products, setProducsts] = useState<IProductOfVendor[] | null>(null);

  useEffect(() => {
    (async () => {
      const data = await productService.getVendorProducts(page);

      setPage(data.current_page);
      setTotalPages(data.total_pages);
      setProducsts(data.data);
    })();
  }, [page]);

  return (
    <>
      {products && products.length > 0 ? (
        <ul>
          {products.map((item) => (
            <VendorProductItem key={item.id} vendorProduct={item} />
          ))}
        </ul>
      ) : (
        <h1 className="text-[26px] flex items-center justify-center">
          Вы еще ничего не продаете
        </h1>
      )}
    </>
  );
};
