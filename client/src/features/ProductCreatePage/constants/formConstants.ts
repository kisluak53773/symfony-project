export const PRODUCT_FORM_FIELDS = [
  {
    title: "Название",
    type: "text",
    placeholder: "",
    name: "title",
    valueType: "string",
    id: "titleId",
    rules: {
      required: "Введите название продукта",
      maxLength: {
        value: 40,
        message: "Название не может быть на столько длинным",
      },
    },
  },
  {
    title: "Еденица измерения продукта",
    type: "text",
    placeholder: "200 г, 1 шт. и т.п.",
    name: "weight",
    valueType: "string",
    id: "weightId",
    rules: {
      required: "Еденицу измерения  продукта",
      maxLength: {
        value: 40,
        message: "Еденица измерения не может быть на столько длинным",
      },
    },
  },
  {
    title: "Описание",
    type: "textarea",
    placeholder: "",
    name: "description",
    valueType: "string",
    id: "descriptionId",
    rules: {
      required: "Введите описание продукта",
      maxLength: {
        value: 1000,
        message: "Описание не может быть на столько длинным",
      },
    },
  },
  {
    title: "Состав",
    type: "textarea",
    placeholder: "",
    name: "compound",
    valueType: "string",
    id: "compoundId",
    rules: {
      required: "Введите состав продукта",
      maxLength: {
        value: 255,
        message: "Состав не может быть на столько длинным",
      },
    },
  },
  {
    title: "Условия хранения",
    type: "textarea",
    placeholder: "",
    name: "storageConditions",
    valueType: "string",
    id: "storageConditionsId",
    rules: {
      required: "Введите условия хранения продукта",
      maxLength: {
        value: 255,
        message: "Условия хранения не могут быть на столько длинными",
      },
    },
  },
];

export const PRODUCT_VENDOR_FORM_FIELDS = [
  ...PRODUCT_FORM_FIELDS,
  {
    title: "Цена",
    type: "text",
    placeholder: "",
    name: "price",
    valueType: "string",
    id: "priceId",
    rules: {
      required: "Введите вашу цену на продукт",
      maxLength: {
        value: 10,
        message: "Цена не может быть на столько длинной",
      },
    },
  },
  {
    title: "Количество продукции на складе",
    type: "text",
    placeholder: "",
    name: "quantity",
    valueType: "number",
    id: "quantityId",
    rules: {
      maxLength: {
        value: 40,
        message: "Нельзя указывать не настоящее количество продукции",
      },
    },
  },
];
