import React, { FC } from "react";
import { type IVendorRequestProps } from "../types";
import { RoutePageWrapper } from "@/components/Routes";
import { RequestDetails } from "./RequsetDetails";

export const VendorRequestPage: FC<IVendorRequestProps> = ({ orderId }) => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading={`Заказ №${orderId}`}>
        <RequestDetails orderId={orderId} />
      </RoutePageWrapper>
    </main>
  );
};
