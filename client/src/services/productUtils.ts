export const productImagePathConverter = (path: string) => {
  return "http://127.0.0.1:8000/images/products/" + path;
};

export const typeImagePathConverter = (path: string) => {
  return "http://127.0.0.1:8000/images/types/" + path;
};
