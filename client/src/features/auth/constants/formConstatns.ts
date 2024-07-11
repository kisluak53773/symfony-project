export const LOGIN_FIELDS = [
  {
    title: "Номер телефона",
    type: "text",
    placeholder: "",
    name: "phone",
    id: "phoneId",
    rules: {
      required: "Введите номер телефона",
      maxLength: {
        value: 40,
        message: "Номер телефоона не может быть настолько длинным",
      },
      pattern: {
        message: "Номер телефона должен быть настоящим",
        value:
          /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/gm,
      },
    },
  },
  {
    title: "Пароль",
    type: "password",
    placeholder: "",
    name: "password",
    id: "passwordId",
    rules: {
      required: "Введите пароль ",
      maxLength: {
        value: 100,
        message: "Нельзя делать на столько длинный пароль",
      },
    },
  },
];

export const REGISTER_FIELDS = [
  {
    title: "Номер телефона",
    type: "text",
    placeholder: "",
    name: "phone",
    id: "phoneId",
    rules: {
      required: "Введите номер телефона",
      maxLength: {
        value: 40,
        message: "Номер телефоона не может быть настолько длинным",
      },
      pattern: {
        message: "Номер телефона должен быть настоящим",
        value:
          /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/gm,
      },
    },
  },
  {
    title: "Пароль",
    type: "password",
    placeholder: "",
    name: "password",
    id: "passwordId",
    rules: {
      required: "Поле пароля не должно быть пустым",
      minLength: {
        value: 4,
        message: "Пароль должен содержать не менее 4 символов",
      },
      maxLength: {
        value: 100,
        message: "Нельзя делать на столько длинный пароль",
      },
    },
  },
  {
    title: "Email",
    type: "text",
    placeholder: "",
    name: "email",
    id: "emailId",
    rules: {
      pattern: { message: "Email должен быть настоящим", value: /^\S+@\S+$/i },
      maxLength: {
        value: 40,
        message: "Email не может быть на столько длинным",
      },
    },
  },
  {
    title: "ФИО",
    type: "text",
    placeholder: "",
    name: "fullName",
    id: "fullNameId",
    rules: {
      maxLength: {
        value: 100,
        message: "Не может ФИО быть на столько длинным",
      },
    },
  },
  {
    title: "Адрес",
    type: "text",
    placeholder: "",
    name: "address",
    id: "addressId",
    rules: {
      maxLength: {
        value: 180,
        message: "Не может адресс быть на столько длинным",
      },
    },
  },
];
