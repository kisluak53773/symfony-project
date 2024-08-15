import { createSlice } from "@reduxjs/toolkit";
import { type IUserInitialState } from "./@types";
import { type RootState } from "@/store/store";

const initialState: IUserInitialState = {
  isAuthorized: false,
};

export const userSlice = createSlice({
  initialState,
  name: "user",
  reducers: {
    logout: (state) => {
      state.isAuthorized = initialState.isAuthorized;
    },
    login: (state) => {
      state.isAuthorized = true;
    },
  },
  extraReducers: (builder) => {},
});

export const getIsAuthorized = (state: RootState) => state.user.isAuthorized;

export const { logout, login } = userSlice.actions;
