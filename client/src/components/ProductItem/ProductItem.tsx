"use client";

import React, { FC } from "react";
import { type IProductItemProps } from "@/types";
import { FiPlus } from "react-icons/fi";
import { useSelector } from "react-redux";
import { getCartProducts } from "@/store/slices/cart";
import { useAppDispatch } from "@/store";
import {
  addProductToCart,
  decreaseQuantity,
  increaseQuantity,
} from "@/store/slices/cart";
import { FiMinus } from "react-icons/fi";
import { productImagePathConverter } from "@/services";

export const ProductItem: FC<IProductItemProps> = ({ product }) => {
  const isItemInCart = useSelector(getCartProducts).find(
    (item) => item.productId === product.id
  );
  const dispatch = useAppDispatch();

  const handleAdd = () => {
    const newProduct = {
      quantity: 1,
      vendorProductId: product.vendorProducts[0].id,
      price: product.vendorProducts[0].price,
      productId: product.id,
      productImage: product.image,
      productWeight: product.weight,
      productTitle: product.title,
      inStock: product.vendorProducts[0].quantity,
    };

    dispatch(addProductToCart(newProduct));
  };

  return (
    <section className=" flex flex-col shadow-xl p-[10px] rounded-lg my-[10px]">
      <img
        className=" w-full"
        src={productImagePathConverter(product.image)}
        width={200}
        height={400}
        alt="Картинка продукта"
      />
      {product.vendorProducts.length > 0 &&
      product.vendorProducts[0].quantity > 0 ? (
        <p className="font-semibold text-red-500 text-[18px]">
          {product.vendorProducts[0].price + "р."}
        </p>
      ) : (
        <p className="font-semibold text-gray-400 text-[18px]">
          Товара нет в наличии
        </p>
      )}
      <p>{product.title}</p>
      <p className=" text-[13px] text-gray-400 mb-[30px]">{product.weight}</p>
      {product.vendorProducts.length > 0 &&
        product.vendorProducts[0].quantity > 0 && (
          <>
            {isItemInCart ? (
              <div className=" flex w-full justify-between h-[34px]">
                <button
                  onClick={() =>
                    dispatch(
                      decreaseQuantity({
                        vendorProductId: isItemInCart.vendorProductId,
                        quantity: 1,
                      })
                    )
                  }
                >
                  <FiMinus size={20} color="black" />
                </button>
                <span>{isItemInCart.quantity} шт.</span>
                <button
                  onClick={() =>
                    dispatch(
                      increaseQuantity({
                        quantity: 1,
                        vendorProductId: isItemInCart.vendorProductId,
                      })
                    )
                  }
                >
                  <FiPlus size={20} color="black" />
                </button>
              </div>
            ) : (
              <button
                onClick={handleAdd}
                className="flex items-center justify-center bg-blue-500 hover:bg-blue-300 rounded-lg w-[64px] h-[34px]"
              >
                <FiPlus size={30} color="white" />
              </button>
            )}
          </>
        )}
    </section>
  );
};
