"use client";

import React, { FC } from "react";
import { type IVendorItemProsp } from "@/features/SpecificProductPage/types";
import { useSelector } from "react-redux";
import {
  decreaseQuantity,
  getCartProducts,
  increaseQuantity,
} from "@/store/slices/cart";
import { FiMinus, FiPlus } from "react-icons/fi";
import { useAppDispatch } from "@/store";
import { addProductToCart } from "@/store/slices/cart";

export const VendorItem: FC<IVendorItemProsp> = ({
  vendor,
  product,
  setProduct,
}) => {
  const isItemInCart = useSelector(getCartProducts).find(
    (item) => item.vendorProductId === vendor.id
  );
  const dispatch = useAppDispatch();

  const decresItesmInStock = () => {
    const vendorProducts = product.vendorProducts.map((item) =>
      item.id === vendor.id
        ? { ...vendor, quantity: vendor.quantity - 1 }
        : item
    );
    const updatedProduct = { ...product, vendorProducts: vendorProducts };
    setProduct(updatedProduct);
  };

  const increseItemsInStock = () => {
    const vendorProducts = product.vendorProducts.map((item) =>
      item.id === vendor.id
        ? { ...vendor, quantity: vendor.quantity + 1 }
        : item
    );
    const updatedProduct = { ...product, vendorProducts: vendorProducts };
    setProduct(updatedProduct);
  };

  const handleAdd = () => {
    const cartProduct = {
      quantity: 1,
      vendorProductId: vendor.id,
      price: vendor.price,
      productId: product.id,
      productImage: product.image,
      productWeight: product.weight,
      productTitle: product.title,
      inStock: vendor.quantity,
    };
    dispatch(addProductToCart(cartProduct));

    decresItesmInStock();
  };

  const handleIncrease = () => {
    if (isItemInCart) {
      dispatch(
        increaseQuantity({
          quantity: 1,
          vendorProductId: isItemInCart.vendorProductId,
        })
      );

      decresItesmInStock();
    }
  };

  const handleDecrease = () => {
    if (isItemInCart) {
      dispatch(
        decreaseQuantity({
          vendorProductId: isItemInCart.vendorProductId,
          quantity: 1,
        })
      );

      increseItemsInStock();
    }
  };

  return (
    <li className=" border-b-[1px] border-b-gray-400 border-solid py-[10px]">
      <div className=" flex justify-between">
        {vendor.quantity > 0 ? (
          <span className=" font-bold text-[20px]">{vendor.price}</span>
        ) : (
          <span className=" font-bold text-[20px] text-gray-400">
            Товара нет в наличии
          </span>
        )}
        <h2>{vendor.vendorTitle}</h2>
      </div>
      {vendor.quantity > 0 && (
        <>
          {isItemInCart ? (
            <div className=" flex w-full justify-between h-[34px]">
              <button onClick={handleDecrease}>
                <FiMinus size={20} color="black" />
              </button>
              <span>{isItemInCart.quantity} шт.</span>
              <button onClick={handleIncrease}>
                <FiPlus size={20} color="black" />
              </button>
            </div>
          ) : (
            <button
              onClick={handleAdd}
              className=" bg-blue-500 my-[10px] hover:bg-blue-300 rounded-lg w-full h-[34px]"
            >
              Добавить в корзину
            </button>
          )}
        </>
      )}
    </li>
  );
};
