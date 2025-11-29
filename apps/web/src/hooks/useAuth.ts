import { getCsrf } from "@/lib/csrf";
import { useQuery } from "@tanstack/vue-query";
import axios from "axios";
import { useRouter } from "vue-router";

export const useAuth = () => {
  const router = useRouter();

  return useQuery({
    queryKey: ["validateUser"],
    queryFn: async () => {
      // await getCsrf();
      const response = await axios.get("/api/v1/auth/validate");
      if (response.status === 401) {
        router.push({ name: "login" });
        return null;
      }
      return response.data.user;
    },
  });
};
