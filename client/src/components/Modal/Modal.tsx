"use client";

import React, { FC, useEffect, useState } from "react";
import { createPortal } from "react-dom";
import { type IModalProps } from "./@types";
import cx from "classnames";

export const Modal: FC<IModalProps> = ({
  children,
  setIsModelActive,
  classes,
}) => {
  const [modalRoot, setModalRoot] = useState<HTMLElement | null>(null);

  useEffect(() => {
    setModalRoot(document.getElementById("modal-root"));
  }, []);

  if (!modalRoot) return null;

  return createPortal(
    <div className={cx(" fixed inset-0 z-30", classes?.modalContainer)}>
      <div
        onClick={() => setIsModelActive(false)}
        className="bg-[rgba(0,0,0,0.7)] h-[100vh] w-[100vw] z-30"
      />
      <div
        className={cx(
          " z-40 fixed inset-0 flex items-center justify-center max-w-[30vw]",
          classes?.modalWindow
        )}
      >
        {children}
      </div>
    </div>,
    modalRoot
  );
};
