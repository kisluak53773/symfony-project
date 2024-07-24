export const VENDOR_FIELDS_TO_UPDATE = [
  {
    title: "Название фирмы",
    type: "text",
    placeholder: "",
    name: "title",
    id: "titleId",
    rules: {
      required: "Название фирмы должно присутствовать",
      maxLength: {
        value: 40,
        message: "Не может быть название фирмы на столько длинным",
      },
    },
  },
  {
    title: "Юридический адрес",
    type: "text",
    placeholder: "",
    name: "address",
    id: "addressId",
    rules: {
      required: "Адрес должно присутствовать",
      maxLength: {
        value: 255,
        message: "Не может адресс быть на столько длинным",
      },
    },
  },
  {
    title: "УПД",
    type: "text",
    placeholder: "",
    name: "inn",
    id: "innId",
    rules: {
      required: "УПД должно присутствовать",
      maxLength: {
        value: 10,
        message: "Не может УПД быть на столько длинным",
      },
    },
  },
  {
    title: "Регистрировавший орган",
    type: "text",
    placeholder: "",
    name: "registrationAuthority",
    id: "registrationAuthorityId",
    rules: {
      required: "Регистрировавший органдолжно присутствовать",
      maxLength: {
        value: 100,
        message:
          "Не может название и адрес Регистрировавшего органа быть на столько длинным",
      },
    },
  },
];
