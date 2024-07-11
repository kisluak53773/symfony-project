"use client";

import React, { FC } from "react";
import { LoginForm } from "./Form";
import { RegisterForm } from "./Form";
import { useSearchParams } from "next/navigation";

export const AuthPage: FC = () => {
  const searchParams = useSearchParams();
  const type = searchParams.get("type");

  return (
    <main className=" flex items-center justify-center h-[80vh]">
      {type === "register" ? <RegisterForm /> : <LoginForm />}
    </main>
  );
};
