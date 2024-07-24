export const PRODUCER_FORM_FIELDS = [
  {
    title: "Название",
    type: "text",
    placeholder: "",
    name: "title",
    id: "titleId",
    rules: {
      required: "Введите название производителя",
      maxLength: {
        value: 40,
        message: "Название не может быть на столько длинным",
      },
    },
  },
  {
    title: "Страна",
    type: "text",
    placeholder: "",
    name: "country",
    id: "countryId",
    rules: {
      required: "Введите страну производителя",
      maxLength: {
        value: 40,
        message: "Название страны не может быть на столько длинным",
      },
    },
  },
  {
    title: "Юридический адресс",
    type: "text",
    placeholder: "",
    name: "address",
    id: "addressId",
    rules: {
      required: "Введите юр. адрес производителя",
      maxLength: {
        value: 100,
        message: "Адресс не может быть на столько длинным",
      },
    },
  },
];
