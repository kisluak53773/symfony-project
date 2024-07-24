import { RoutePageWrapper } from "@/components/Routes";
import React, { FC } from "react";
import { ProfileForm } from "./Form";

export const ProflieUpdatePage: FC = () => {
  return (
    <main className=" w-full">
      <RoutePageWrapper heading="Данные вашего профиля">
        <ProfileForm />
      </RoutePageWrapper>
    </main>
  );
};
