import { IUser } from "@/services/user";

export interface IUserInitialState {
  user: IUser | null;
  error: string;
}
