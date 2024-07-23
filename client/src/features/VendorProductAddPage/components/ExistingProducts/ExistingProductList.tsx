"use client";

import React, { FC, useEffect, useState } from "react";
import {
  productService,
  type IProductVendorDoesNotSell,
} from "@/services/product";
import { ExistingProductItem } from "./ExistingProductItem";

export const ExistingProductList: FC = () => {
  const [products, setProducts] = useState<IProductVendorDoesNotSell[] | null>(
    null
  );
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  useEffect(() => {
    (async () => {
      const data = await productService.getProductsVendorDoesNotSell(page);

      setProducts(data.data);
      setPage(data.current_page);
      setTotalPages(data.total_pages);
    })();
  }, [page]);

  return (
    <>
      {products && products.length > 0 ? (
        <ul>
          {products.map((item) => (
            <ExistingProductItem key={item.id} product={item} />
          ))}
        </ul>
      ) : (
        <h1 className="text-[26px] flex items-center justify-center">
          Таких продуктов нету
        </h1>
      )}
    </>
  );
};
