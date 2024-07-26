"use client";

import React, { FC, useState } from "react";
import { HeaderImage } from "../HeaderImage";
import { Modal } from "@/components/Modal";
import { HeaderSearchModal } from "./HeaderSearchModal";

export const HeaderSeach: FC = () => {
  const [isMoadlActive, setIsModalActive] = useState(false);

  return (
    <div className=" rounded-lg p-[10px] bg-gray-100 w-[45%] flex gap-[5px]">
      <HeaderImage type="search" />
      <button
        onClick={() => setIsModalActive(!isMoadlActive)}
        className=" w-full bg-gray-100 focus:outline-none"
      />
      {isMoadlActive && (
        <Modal
          setIsModelActive={setIsModalActive}
          classes={{
            modalWindow: "absolute top-0 left-0 h-[8vh]",
          }}
        >
          <HeaderSearchModal />
        </Modal>
      )}
    </div>
  );
};
