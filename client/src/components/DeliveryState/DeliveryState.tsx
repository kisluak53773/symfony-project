"use client";

import React, { FC } from "react";
import { ORDER_STATUSES } from "@/constants";
import { type IDeliveryStateProps } from "@/types";
import { Canceled } from "./Canceled";
import { Delivered } from "./Delivered";
import { OnTheWay } from "./OnTheWay";
import { Processed } from "./Processed";

export const DeliveryState: FC<IDeliveryStateProps> = ({ status }) => {
  switch (status) {
    case ORDER_STATUSES.ORDER_CANCELED:
      return <Canceled />;
    case ORDER_STATUSES.ORDER_DELIVERED:
      return <Delivered />;
    case ORDER_STATUSES.ORDER_ON_THE_WAY:
      return <OnTheWay />;
    case ORDER_STATUSES.ORDER_PROCESSED:
      return <Processed />;
  }
};
