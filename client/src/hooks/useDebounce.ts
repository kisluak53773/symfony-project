"use client";

import { useState, useEffect } from "react";

export const useDebounce = <T>(value: T, delay = 500) => {
  const [debounceValue, setDebounceValue] = useState<T>();

  useEffect(() => {
    const debounce = setTimeout(() => {
      setDebounceValue(value);
    }, delay);

    return () => clearTimeout(debounce);
  }, [value, delay]);

  return debounceValue;
};
