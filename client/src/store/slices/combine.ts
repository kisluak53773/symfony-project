"use client";

import { combineReducers } from "redux";
import { cartSlice } from "./cart";
import { userSlice } from "./user";
import { favoriteSlice } from "./favorite";

export const rootReducer = combineReducers({
  [cartSlice.name]: cartSlice.reducer,
  [userSlice.name]: userSlice.reducer,
  [favoriteSlice.name]: favoriteSlice.reducer,
});
