"use client";

import { combineReducers } from "redux";
import { cartSlice } from "./cart";

export const rootReducer = combineReducers({
  [cartSlice.name]: cartSlice.reducer,
});
