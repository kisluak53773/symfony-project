import { PropsWithChildren } from "react";

export interface IRouteItemProps {
  href: string;
  title: string;
}

export interface IRoutePageWrapperProps extends PropsWithChildren {
  heading: string;
}
