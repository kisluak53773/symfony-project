import Cookies from "js-cookie";

export enum EnumTokens {
  "ACCESS_TOKEN" = "token",
  "REFRESH_TOKEN" = "refresh_token",
}

export const getAccessToken = () => {
  const accessToken = Cookies.get(EnumTokens.ACCESS_TOKEN);

  return accessToken ?? null;
};

export const getRefreshToken = () => {
  const refreshToken = Cookies.get(EnumTokens.REFRESH_TOKEN);

  return refreshToken ?? null;
};

export const setTokens = (accessToken: string, refreshToken: string) => {
  Cookies.set(EnumTokens.ACCESS_TOKEN, accessToken, {
    domain: "localhost",
    sameSite: "strict",
    expires: 1,
  });
  Cookies.set(EnumTokens.REFRESH_TOKEN, refreshToken, {
    domain: "localhost",
    sameSite: "Strict",
    expires: 30,
  });
};

export const removeTokens = () => {
  Cookies.remove(EnumTokens.ACCESS_TOKEN);
  Cookies.remove(EnumTokens.REFRESH_TOKEN);
};
