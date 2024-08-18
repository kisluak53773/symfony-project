"use client";

import React, { FC, useEffect, useState } from "react";
import { type IProductDataProps } from "../../types";
import { productService } from "@/services/product";
import { ProductDescription } from "./ProductDescription";
import { type ISpecificPoduct } from "@/services/product";

export const ProductData: FC<IProductDataProps> = async ({ productId }) => {
  const [product, setProduct] = useState<ISpecificPoduct | null>(null);

  useEffect(() => {
    (async () => {
      const product = await productService.getProductById(productId);

      setProduct(product);
    })();
  }, [productId]);

  return (
    <>
      {product && (
        <>
          <ProductDescription product={product} setProduct={setProduct} />
        </>
      )}
    </>
  );
};
