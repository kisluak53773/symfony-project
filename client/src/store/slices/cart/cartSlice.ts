"use client";

import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { type ICart, type ICartProduct } from "./@types";
import { RootState } from "@/store/store";
import { type IProduct } from "@/services/product";

const initialState: ICart = {
  producst: [],
  error: "",
};

export const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    addToCart: (state, action: PayloadAction<ICartProduct>) => {
      const itemInCart = state.producst.find(
        (product) => product.id === action.payload.id
      );
      if (itemInCart) {
        itemInCart.quantity++;
      } else {
        state.producst.push({ ...action.payload, quantity: 1 });
      }
    },
    removeFromCart: (state, action: PayloadAction<IProduct>) => {
      const filteredItems = state.producst.filter(
        (product) => product.id !== action.payload.id
      );
      state.producst = filteredItems;
    },
    removeAllFromCart: (state) => {
      state.producst = initialState.producst;
    },
    incrementQuantity: (state, action: PayloadAction<ICartProduct>) => {
      const itemInCart = state.producst.find(
        (product) => product.id === action.payload.id
      );
      if (itemInCart) itemInCart.quantity++;
    },
    decrementQuantity: (state, action: PayloadAction<ICartProduct>) => {
      const itemInCart = state.producst.find(
        (product) => product.id === action.payload.id
      );
      if (itemInCart) {
        if (itemInCart.quantity !== 1) {
          itemInCart.quantity--;
        } else {
          const filteredItems = state.producst.filter(
            (product) => product.id !== itemInCart.id
          );
          state.producst = filteredItems;
        }
      }
    },
  },
  extraReducers: (builder) => {},
});

export const getCartError = (state: RootState) => state.cart.error;
export const getCartProducts = (state: RootState) => state.cart.producst;

export const {
  addToCart,
  removeFromCart,
  incrementQuantity,
  decrementQuantity,
  removeAllFromCart,
} = cartSlice.actions;
