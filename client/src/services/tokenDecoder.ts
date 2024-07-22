import { jwtDecode } from "jwt-decode";
import { getAccessToken } from "./cookie";
import { type IJwtPayload } from "@/types";

export const getTokenPayload = () => {
  const accessToken = getAccessToken();

  return accessToken ? jwtDecode<IJwtPayload>(accessToken) : null;
};
