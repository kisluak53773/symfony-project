"use client";

import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import { type ICart, type ICartItemCreate } from "./@types";
import { type RootState } from "@/store/store";
import { cartService, type ICartUpdateData } from "@/services/cart";
import { errorCatch } from "@/services/axios";

const initialState: ICart = {
  producst: [],
  error: "",
};

export const addProductToCart = createAsyncThunk(
  "addProductToCart",
  async (action: ICartItemCreate) => {
    const data = await cartService.addProductToCart({
      vendorProductId: action.vendorProductId,
      quantity: action.quantity,
    });

    return { ...action, id: data.id };
  }
);

export const decreaseQuantity = createAsyncThunk(
  "decreaseQuantityOfProduct",
  async (action: ICartUpdateData) => {
    await cartService.decrementProductQuantity(action);

    return action;
  }
);

export const increaseQuantity = createAsyncThunk(
  "increaseQuantityOfProduct",
  async (action: ICartUpdateData) => {
    const data = await cartService.incrementProductQuantity(action);

    return { ...action, quantity: data.quantity };
  }
);

export const deleteProductFromCart = createAsyncThunk(
  "deleteProdcutFromCart",
  async (action: { vendorProductId: number }) => {
    await cartService.deleteProductFromCart(action.vendorProductId);

    return action;
  }
);

export const deleteAllProducstInCart = createAsyncThunk(
  "deleteAllProdcutsInCart",
  async () => {
    const data = await cartService.deleteAllProdcutsFromCart();

    return data;
  }
);

export const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    emptyCart: (state) => {
      state.producst = initialState.producst;
    },
  },
  extraReducers: (builder) => {
    builder.addCase(addProductToCart.fulfilled, (state, action) => {
      state.producst.push(action.payload);
    });
    builder.addCase(addProductToCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(decreaseQuantity.fulfilled, (state, action) => {
      const itemInCart = state.producst.find(
        (product) => product.vendorProductId === action.payload.vendorProductId
      );
      if (itemInCart) {
        if (itemInCart.quantity > action.payload.quantity) {
          itemInCart.quantity -= action.payload.quantity;
        } else {
          const filteredItems = state.producst.filter(
            (product) => product.id !== itemInCart.id
          );
          state.producst = filteredItems;
        }
      }
    });
    builder.addCase(decreaseQuantity.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(increaseQuantity.fulfilled, (state, action) => {
      const cartItem = state.producst.find(
        (item) => item.vendorProductId === action.payload.vendorProductId
      );

      if (cartItem) {
        cartItem.quantity += action.payload.quantity;
      }
    });
    builder.addCase(increaseQuantity.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(deleteProductFromCart.fulfilled, (state, action) => {
      const filteredArray = state.producst.filter(
        (item) => item.vendorProductId !== action.payload.vendorProductId
      );
      state.producst = filteredArray;
    });
    builder.addCase(deleteProductFromCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(deleteAllProducstInCart.fulfilled, (state) => {
      state.producst = initialState.producst;
    });
    builder.addCase(deleteAllProducstInCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
  },
});

export const getCartError = (state: RootState) => state.cart.error;
export const getCartProducts = (state: RootState) => state.cart.producst;

export const { emptyCart } = cartSlice.actions;
