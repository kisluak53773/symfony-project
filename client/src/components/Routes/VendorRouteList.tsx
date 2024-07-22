import React, { FC } from "react";
import { VENDOR_ROUTS } from "@/constants";
import { RouteItem } from "./RouteItem";

export const VendorRouteList: FC = () => {
  return (
    <nav className=" min-h-[87vh] p-[40px]">
      <ul>
        {VENDOR_ROUTS.map((item) => (
          <RouteItem key={item.id} href={item.href} title={item.title} />
        ))}
      </ul>
    </nav>
  );
};
