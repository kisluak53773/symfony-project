import { type IType } from "./type";
import { type IProducer } from "@/services/producer";
import { type IOption } from "@/types";

export const convertToFormData = (data: any): FormData => {
  const formData = new FormData();

  for (let key in data) {
    formData.append(key, data[key]);
  }

  return formData;
};

export const convertToReactSelectOptions = (
  data: IType[] | IProducer[]
): IOption[] => {
  return data.map((item) => {
    return { value: `${item.id}`, label: item.title };
  });
};
