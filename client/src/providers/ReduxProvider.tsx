"use client";

import React, { FC, PropsWithChildren } from "react";
import { store } from "@/store";
import { Provider } from "react-redux";
import { persistStore } from "redux-persist";
import { PersistGate } from "redux-persist/integration/react";

export const ReduxProvider: FC<PropsWithChildren> = ({ children }) => {
  const persistor = persistStore(store);

  return (
    <Provider store={store}>
      <PersistGate loading={null} persistor={persistor}>
        {children}
      </PersistGate>
    </Provider>
  );
};
