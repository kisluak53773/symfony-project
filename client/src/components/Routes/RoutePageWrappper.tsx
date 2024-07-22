import React, { FC } from "react";
import { type IRoutePageWrapperProps } from "./@types";

export const RoutePageWrapper: FC<IRoutePageWrapperProps> = ({
  children,
  heading,
}) => {
  return (
    <article className=" py-[40px] w-[80%]">
      <h1 className=" text-[28px] font-semibold mb-[40px]">{heading}</h1>
      {children}
    </article>
  );
};
