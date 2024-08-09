import { getTokenPayload } from "./tokenDecoder";
import { HEADER_UNATHORIZED_LINKS } from "@/constants";
import { HEADER_ADMIN_LINKS } from "@/constants";
import { HEADER_VENDOR_LINKS } from "@/constants";
import { HEADER_USER_LINKS } from "@/constants";
import { ROLES } from "@/constants";

export const getHeaderLinks = () => {
  const tokenPayload = getTokenPayload();

  if (!tokenPayload) {
    return HEADER_UNATHORIZED_LINKS;
  } else if (tokenPayload.roles.includes(ROLES.ROLE_ADMIN)) {
    return HEADER_ADMIN_LINKS;
  } else if (tokenPayload.roles.includes(ROLES.ROLE_VENDOR)) {
    return HEADER_VENDOR_LINKS;
  } else {
    return HEADER_USER_LINKS;
  }
};
