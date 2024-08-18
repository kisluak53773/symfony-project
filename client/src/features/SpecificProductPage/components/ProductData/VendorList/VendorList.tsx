import React, { FC } from "react";
import { type IVendorListProps } from "@/features/SpecificProductPage/types";
import { VendorItem } from "./VendorItem";

export const VendorList: FC<IVendorListProps> = ({ product, setProduct }) => {
  return (
    <section className=" min-w-[400px]">
      <h2 className=" font-semibold mb-[20px]">Список продавцов</h2>
      {product.vendorProducts.length > 0 ? (
        <ul>
          {product.vendorProducts.map((item) => (
            <VendorItem
              key={item.id}
              vendor={item}
              product={product}
              setProduct={setProduct}
            />
          ))}
        </ul>
      ) : (
        <div>
          <p className=" font-semibold text-[20px]">Товара нет в наличии</p>
        </div>
      )}
    </section>
  );
};
