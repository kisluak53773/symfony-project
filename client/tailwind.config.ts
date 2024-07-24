import type { Config } from "tailwindcss";
import { COLORS } from "./src/constants";

const config: Config = {
  content: ["./src/**/*.{js,ts,jsx,tsx,mdx}"],
  theme: {
    extend: {
      colors: COLORS,
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
      keyframes: {
        "trans-right": {
          "0%": { opacity: "0", transform: "translateX(200px)" },
          "100%": { opacity: "1", transform: "translateX(0)" },
        },
      },
      animation: {
        "trans-right": "trans-right 0.2s linear",
      },
    },
  },
  plugins: [],
};
export default config;
