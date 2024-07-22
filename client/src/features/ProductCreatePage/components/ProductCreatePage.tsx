"use client";

import { getTokenPayload } from "@/services/tokenDecoder";
import React, { FC } from "react";
import { VenndorForm } from "./Form";
import { ROLES } from "@/constants";
import { RoutePageWrapper } from "@/components/Routes";

export const ProductCreatePage: FC = () => {
  const roles = getTokenPayload()?.roles;

  return (
    <main className="w-full">
      <RoutePageWrapper heading="Создание нового продукта">
        {roles && roles.includes(ROLES.ROLE_VENDOR) ? (
          <VenndorForm />
        ) : (
          <div>Create page</div>
        )}
      </RoutePageWrapper>
    </main>
  );
};
