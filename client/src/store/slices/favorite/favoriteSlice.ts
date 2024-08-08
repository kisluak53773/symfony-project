"use client";

import { createSlice, createAsyncThunk, PayloadAction } from "@reduxjs/toolkit";
import { type RootState } from "@/store/store";
import { errorCatch } from "@/services/axios";
import { type IFavorite } from "./@types";
import { favoriteService } from "@/services/favorite";
import { type IProduct } from "@/services/product";

const initialState: IFavorite = {
  favoriteProducts: [],
  error: "",
};

export const fetchFavoriteProducts = createAsyncThunk(
  "fetchFavoriteProducts",
  async () => {
    const data = await favoriteService.getAllFavorite();

    return data;
  }
);

export const addProductToFavorite = createAsyncThunk(
  "addProductToFavorite",
  async (action: IProduct, thunkAPI) => {
    thunkAPI.dispatch(addToFavoriteProductsReducer(action));
    await favoriteService.addToFavorite(action.id);

    return action;
  }
);

export const deleteProductFromFavorite = createAsyncThunk(
  "deleteProductFromFavorite",
  async (action: { productId: number }, thunkAPI) => {
    thunkAPI.dispatch(deleteFromFavoriteReducer(action));
    await favoriteService.deleteFromFavorite(action.productId);

    return action;
  }
);

export const favoriteSlice = createSlice({
  name: "favorite",
  initialState,
  reducers: {
    emptyFavorite: (state) => {
      state.favoriteProducts = initialState.favoriteProducts;
    },
    addToFavoriteProductsReducer: (state, action: PayloadAction<IProduct>) => {
      const inCart = state.favoriteProducts.find(
        (item) => item.id === action.payload.id
      );

      if (!inCart) {
        state.favoriteProducts.push(action.payload);
      }
    },
    deleteFromFavoriteReducer: (
      state,
      action: PayloadAction<{ productId: number }>
    ) => {
      const filteredArray = state.favoriteProducts.filter(
        (item) => item.id !== action.payload.productId
      );
      state.favoriteProducts = filteredArray;
    },
  },
  extraReducers: (builder) => {
    builder.addCase(fetchFavoriteProducts.fulfilled, (state, action) => {
      state.error = initialState.error;
      state.favoriteProducts = action.payload;
    });
    builder.addCase(fetchFavoriteProducts.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(addProductToFavorite.fulfilled, (state, action) => {
      state.error = initialState.error;
    });
    builder.addCase(addProductToFavorite.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
    builder.addCase(deleteProductFromFavorite.fulfilled, (state, action) => {
      state.error = initialState.error;
    });
    builder.addCase(deleteProductFromFavorite.rejected, (state, action) => {
      state.error = errorCatch(action.error);
    });
  },
});

export const getFavoriteError = (state: RootState) => state.favorite.error;
export const getFavoriteProducts = (state: RootState) =>
  state.favorite.favoriteProducts;

export const {
  emptyFavorite,
  deleteFromFavoriteReducer,
  addToFavoriteProductsReducer,
} = favoriteSlice.actions;
