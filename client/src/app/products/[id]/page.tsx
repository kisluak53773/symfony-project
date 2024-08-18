import { SpecificProductPage } from "@/features/SpecificProductPage";
import { FC } from "react";

interface IParams {
  params: { id: number };
}

const SpecificProduct: FC<IParams> = ({ params }) => (
  <SpecificProductPage productId={params.id} />
);

export default SpecificProduct;
