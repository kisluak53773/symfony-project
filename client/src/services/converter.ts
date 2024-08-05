import { type IType } from "./type";
import { type IProducer } from "@/services/producer";
import { type IOption } from "@/types";
import { type ISort } from "@/features/ProductsPage/types";

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

export const convertArrayToQuerryParams = (title: string, data: any[]) => {
  let str = "";

  for (const item of data) {
    str += `&${title}[]=${item}`;
  }

  return str;
};

export const convertSortToQuerryParams = (sort: ISort) => {
  return `&${sort.tag}=${sort.value}`;
};

export const formatDateString = (dateString: string) => {
  const date = new Date(dateString);

  const year = date.getUTCFullYear();
  const month = String(date.getUTCMonth() + 1).padStart(2, "0");
  const day = String(date.getUTCDate()).padStart(2, "0");
  let hours = date.getUTCHours();
  const minutes = String(date.getUTCMinutes()).padStart(2, "0");

  return `${year}.${month}.${day} ${hours}:${minutes}`;
};
