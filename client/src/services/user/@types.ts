import { ROLES } from "@/constants/projectConstants";

export interface IUser {
  id: number;
  email: string | null;
  fullName: string | null;
  address: string | null;
  phone: string;
  roles: ROLES[];
  createdAt: Date;
  updatedAt: Date;
}

export interface IProfileUpdate {
  phone: string;
  email?: string;
  address?: string;
  fullName?: string;
}
