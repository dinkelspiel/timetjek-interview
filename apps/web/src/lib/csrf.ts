import axios from "axios";

export const getCsrf = async () => {
  await axios.get("/sanctum/csrf-cookie", {
    withCredentials: true,
  });
};
