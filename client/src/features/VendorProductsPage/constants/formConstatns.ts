export const VENDOR_PRODUCT_FIELDS = [
  {
    title: "Цена",
    type: "text",
    placeholder: "",
    name: "price",
    valueType: "string",
    id: "priceId",
    rules: {
      required: "Цена не может быть пустой",
      maxLength: {
        value: 40,
        message: "Название не может быть на столько длинным",
      },
    },
  },
  {
    title: "Количество на складе",
    type: "text",
    placeholder: "",
    name: "quantity",
    valueType: "number",
    id: "quantityId",
    rules: {
      required: "Нельзя оставлять поле с количеством пустым",
      maxLength: {
        value: 40,
        message: "Название не может быть на столько длинным",
      },
    },
  },
];
