"use client";

import { ROLES } from "@/constants";
import { getTokenPayload } from "@/services/tokenDecoder";
import { useRouter } from "next/router";
import React, { FC, useEffect } from "react";
import { RouteItem } from "./RouteItem";
import { ADMIN_ROUTES } from "@/constants";
import { useSelector } from "react-redux";
import { getIsAuthorized } from "@/store/slices/user";

export const AdminRouteList: FC = () => {
  const router = useRouter();
  const isAuthorized = useSelector(getIsAuthorized);

  useEffect(() => {
    const tokenPayload = getTokenPayload();

    if (!tokenPayload || !tokenPayload.roles.includes(ROLES.ROLE_ADMIN)) {
      router.replace("/");
    }
  }, [isAuthorized]);

  return (
    <aside className=" min-h-[87vh] p-[40px]">
      <nav>
        <ul>
          {ADMIN_ROUTES.map((item) => (
            <RouteItem key={item.id} href={item.href} title={item.title} />
          ))}
        </ul>
      </nav>
    </aside>
  );
};
