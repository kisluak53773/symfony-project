export interface IRegisterData {
  password: string;
  phone: string;
  email?: string;
  address?: string;
  fullName?: string;
}

export interface ILoginData {
  phone: string;
  password: string;
}

export interface ITokens {
  token: string;
  refresh_token: string;
}
