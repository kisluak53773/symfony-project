export interface IPaginationProps {
  totalPageCount: number;
  siblingCount?: number;
  currentPage: number;
  setPage: React.Dispatch<React.SetStateAction<number>>;
}
