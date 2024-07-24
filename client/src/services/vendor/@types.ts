export interface IVendor {
  title: string;
  address: string;
  inn: string;
  registrationAuthority: string;
  registrationDate: Date;
  registrationCertificateDate: Date;
}

export interface IVendorToUpdate
  extends Omit<IVendor, "registrationDate" | "registrationCertificateDate"> {}
