import React, { FC } from "react";
import { RoutePageWrapper } from "@/components/Routes";
import { RequestList } from "./RequsetList";

export const OrdersRequestPage: FC = () => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading="Ваши заявки">
        <RequestList />
      </RoutePageWrapper>
    </main>
  );
};
