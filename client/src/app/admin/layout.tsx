import { AdminRouteList } from "@/components/Routes";

export default function UserLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <main className=" flex bg-white">
      <AdminRouteList />
      {children}
    </main>
  );
}
