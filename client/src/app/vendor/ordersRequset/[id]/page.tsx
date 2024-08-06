import { VendorRequestPage } from "@/features/VendorRequsetPage";
import { FC } from "react";

interface IParams {
  params: { id: number };
}

const VendorRequset: FC<IParams> = ({ params }) => (
  <VendorRequestPage orderId={params.id} />
);

export default VendorRequset;
