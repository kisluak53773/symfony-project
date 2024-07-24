import { VendorRouteList } from "@/components/Routes";

export default function DetailsLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <main className=" flex bg-white">
      <VendorRouteList />
      {children}
    </main>
  );
}
