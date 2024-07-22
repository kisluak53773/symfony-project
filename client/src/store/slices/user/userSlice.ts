import { createSlice, createAsyncThunk } from "@reduxjs/toolkit";
import { type IUserInitialState } from "./@types";
import { type RootState } from "@/store/store";
import { userService } from "@/services/user";
import { errorCatch } from "@/services/axios";

const initialState: IUserInitialState = {
  user: null,
  error: "",
};

export const fetchUser = createAsyncThunk("user/fetch", async () => {
  const user = await userService.getCurrentUser();
  return user;
});

export const userSlice = createSlice({
  initialState,
  name: "user",
  reducers: {},
  extraReducers: (builder) => {
    builder.addCase(fetchUser.fulfilled, (state, action) => {
      state.user = action.payload;
      state.error = initialState.error;
    });
    builder.addCase(fetchUser.rejected, (state, action) => {
      state.user = initialState.user;
      state.error = errorCatch(action.error);
    });
  },
});

export const getUser = (state: RootState) => state.user.user;
export const getUserError = (state: RootState) => state.user.error;

export const {} = userSlice.actions;
