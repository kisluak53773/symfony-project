"use client";

import { createSlice, createAsyncThunk, PayloadAction } from "@reduxjs/toolkit";
import { type ICart, type ICartItemCreate } from "./@types";
import { type RootState } from "@/store/store";
import { cartService, type ICartUpdateData } from "@/services/cart";
import { errorCatch } from "@/services/axios";

const initialState: ICart = {
  producst: [],
  error: "",
};

export const getCart = createAsyncThunk("getCart", async () => {
  const data = await cartService.getCart();

  return data;
});

export const addProductToCart = createAsyncThunk(
  "addProductToCart",
  async (action: ICartItemCreate, thunkAPI) => {
    thunkAPI.dispatch(addToCartReducer(action));
    const data = await cartService.addProductToCart({
      vendorProductId: action.vendorProductId,
      quantity: action.quantity,
    });

    return { ...action, id: data.id };
  }
);

export const decreaseQuantity = createAsyncThunk(
  "decreaseQuantityOfProduct",
  async (action: ICartUpdateData, thunkAPI) => {
    thunkAPI.dispatch(decreaseProductQuantityReducer(action));
    await cartService.decrementProductQuantity(action);

    return action;
  }
);

export const increaseQuantity = createAsyncThunk(
  "increaseQuantityOfProduct",
  async (action: ICartUpdateData, thunkAPI) => {
    const currentState = thunkAPI.getState() as RootState;
    const inStock = currentState.cart.producst.find(
      (item) => item.vendorProductId === action.vendorProductId
    )?.inStock;

    if (inStock) {
      action =
        inStock > action.quantity ? action : { ...action, quantity: inStock };
      thunkAPI.dispatch(increaseProductQuantityReducer(action));
      await cartService.incrementProductQuantity(action);

      return action;
    }
  }
);

export const deleteProductFromCart = createAsyncThunk(
  "deleteProdcutFromCart",
  async (action: { vendorProductId: number }, thunkAPI) => {
    thunkAPI.dispatch(deleteProdcutReducer(action));
    await cartService.deleteProductFromCart(action.vendorProductId);

    return action;
  }
);

export const deleteAllProducstInCart = createAsyncThunk(
  "deleteAllProdcutsInCart",
  async (_, thunkAPI) => {
    thunkAPI.dispatch(emptyCart());
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
    addToCartReducer: (state, action: PayloadAction<ICartItemCreate>) => {
      state.producst.push({ ...action.payload, id: 123 });
    },
    increaseProductQuantityReducer: (
      state,
      action: PayloadAction<ICartUpdateData>
    ) => {
      const cartItem = state.producst.find(
        (item) => item.vendorProductId === action.payload.vendorProductId
      );

      if (cartItem) {
        cartItem.quantity += action.payload.quantity;
      }
    },
    decreaseProductQuantityReducer: (
      state,
      action: PayloadAction<ICartUpdateData>
    ) => {
      const itemInCart = state.producst.find(
        (product) => product.vendorProductId === action.payload.vendorProductId
      );
      if (itemInCart) {
        if (itemInCart.quantity > action.payload.quantity) {
          itemInCart.quantity -= action.payload.quantity;
          itemInCart.inStock += action.payload.quantity;
        } else {
          const filteredItems = state.producst.filter(
            (product) => product.id !== itemInCart.id
          );
          state.producst = filteredItems;
        }
      }
    },
    deleteProdcutReducer: (
      state,
      action: PayloadAction<{ vendorProductId: number }>
    ) => {
      const filteredArray = state.producst.filter(
        (item) => item.vendorProductId !== action.payload.vendorProductId
      );
      state.producst = filteredArray;
    },
  },
  extraReducers: (builder) => {
    builder.addCase(getCart.fulfilled, (state, action) => {
      state.error = initialState.error;
      state.producst = action.payload;
    });
    builder.addCase(getCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(addProductToCart.fulfilled, (state, action) => {
      state.error = initialState.error;
      const itemInCart = state.producst.find(
        (item) => item.vendorProductId === action.payload.vendorProductId
      );
      if (itemInCart) itemInCart.id = action.payload.id;
    });
    builder.addCase(addProductToCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(decreaseQuantity.fulfilled, (state, action) => {
      state.error = initialState.error;
    });
    builder.addCase(decreaseQuantity.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(increaseQuantity.fulfilled, (state, action) => {
      state.error = initialState.error;
    });
    builder.addCase(increaseQuantity.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(deleteProductFromCart.fulfilled, (state, action) => {
      state.error = initialState.error;
    });
    builder.addCase(deleteProductFromCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(deleteAllProducstInCart.fulfilled, (state) => {
      state.error = initialState.error;
    });
    builder.addCase(deleteAllProducstInCart.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
  },
});

export const getCartError = (state: RootState) => state.cart.error;
export const getCartProducts = (state: RootState) => state.cart.producst;

export const {
  emptyCart,
  decreaseProductQuantityReducer,
  increaseProductQuantityReducer,
  deleteProdcutReducer,
  addToCartReducer,
} = cartSlice.actions;
