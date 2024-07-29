"use client";

import React, { FC, useEffect, useState } from "react";
import { type IProduct } from "@/services/product";
import { productService } from "@/services/product";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import { ProductItem } from "@/components/ProductItem";

export const MostPopularProducts: FC = () => {
  const [products, setProducts] = useState<IProduct[] | null>(null);

  useEffect(() => {
    (async () => {
      const data = await productService.getProducts({});
      setProducts(data.data);
    })();
  }, []);

  return (
    <article className="felx flex-col mx-[18vw]">
      <h1 className="text-[24px] font-semibold mb-[10px] mt-[40px]">
        Самое популярное
      </h1>
      <Swiper
        modules={[Navigation]}
        spaceBetween={10}
        navigation={true}
        slidesPerView={4}
        loop
      >
        {products &&
          products.map((item) => (
            <SwiperSlide key={item.id}>
              <ProductItem product={item} />
            </SwiperSlide>
          ))}
      </Swiper>
    </article>
  );
};
