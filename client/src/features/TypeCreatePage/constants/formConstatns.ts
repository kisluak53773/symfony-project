export const TYPE_FORM_FIELDS = [
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
];
