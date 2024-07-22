"use client";

import { combineReducers } from "redux";
import { cartSlice } from "./cart";
import { userSlice } from "./user";

export const rootReducer = combineReducers({
  [cartSlice.name]: cartSlice.reducer,
  [userSlice.name]: userSlice.reducer,
});
