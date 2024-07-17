import { PropsWithChildren } from "react";

export interface IModalProps extends PropsWithChildren {
  setIsModelActive: React.Dispatch<React.SetStateAction<boolean>>;
  classes?: {
    modalWindow?: string;
  };
}
