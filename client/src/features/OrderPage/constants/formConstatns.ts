import { PAYMENT_MENTHODS } from "@/constants";

export const ORDER_FORM_CONSTANTS = [
  {
    title: "Выберите время доставки",
    type: "datetime-local",
    placeholder: "",
    name: "deliveryTime",
    id: "deliveryTimeId",
    rules: {
      required: "Время доставки обязательно к выбору",
    },
  },
  {
    title: "Коментарий",
    type: "textarea",
    placeholder: "",
    name: "comment",
    id: "commentId",
    rules: {
      maxLength: {
        value: 40,
        message: "Нельзя отправлять настолько длинный коментарий",
      },
    },
  },
];

export const PAYMENY_METHODS_FORM_CONSTANTS = [
  {
    title: "Картой курьеру",
    type: "radio",
    value: PAYMENT_MENTHODS.PAYMENT_CARD,
    name: "paymentMethod",
    placeholder: "",
    id: "cashId",
    rules: {
      required: "Способ оплаты должен быть выбран",
    },
  },
  {
    title: "Наличными",
    type: "radio",
    value: PAYMENT_MENTHODS.PAYMENT_CASH,
    name: "paymentMethod",
    placeholder: "",
    id: "cardId",
    rules: {
      required: "Способ оплаты должен быть выбран",
    },
  },
];
