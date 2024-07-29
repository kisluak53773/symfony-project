import { Dispatch, RefObject, SetStateAction } from "react";
import { type IType } from "@/services/type";
import { type IProducer } from "@/services/producer";

export interface IFilterProps {
  setTypesFilter: Dispatch<SetStateAction<number[]>>;
  typesFilter: number[];
  setProducersFilter: Dispatch<SetStateAction<number[]>>;
  producersFilter: number[];
  setSort: Dispatch<SetStateAction<ISort>>;
  sort: ISort;
}

export interface ISort {
  id: number;
  title: string;
  value: string;
  tag: string;
}

export interface IFilterListProps {
  setFilter: Dispatch<SetStateAction<number[]>>;
  filter: number[];
  setOnMouseFilterOver: Dispatch<SetStateAction<boolean>>;
  filterRef: RefObject<HTMLParagraphElement>;
  filterItemms: IType[] | IProducer[];
}

export interface ISortListProps {
  selectedSort: ISort;
  setSort: Dispatch<SetStateAction<ISort>>;
  setOnMouseSortOver: Dispatch<SetStateAction<boolean>>;
  sortRef: RefObject<HTMLParagraphElement>;
}
