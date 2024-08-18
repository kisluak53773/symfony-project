"use client";

import React, { FC, useEffect } from "react";
import { VENDOR_ROUTS } from "@/constants";
import { RouteItem } from "./RouteItem";
import { getTokenPayload } from "@/services/tokenDecoder";
import { useRouter } from "next/navigation";
import { ROLES } from "@/constants";
import { useSelector } from "react-redux";
import { getIsAuthorized } from "@/store/slices/user";

export const VendorRouteList: FC = () => {
  const router = useRouter();
  const isAuthorized = useSelector(getIsAuthorized);

  useEffect(() => {
    const tokenPayload = getTokenPayload();

    if (!tokenPayload || !tokenPayload.roles.includes(ROLES.ROLE_VENDOR)) {
      router.replace("/");
    }
  }, [isAuthorized]);

  return (
    <aside className=" min-h-[87vh] p-[40px]">
      <nav>
        <ul>
          {VENDOR_ROUTS.map((item) => (
            <RouteItem key={item.id} href={item.href} title={item.title} />
          ))}
        </ul>
      </nav>
    </aside>
  );
};
