<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useLocation } from "@/hooks/useLocation";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import axios from "axios";
import { toast } from "vue-sonner";

const queryClient = useQueryClient();

const { latitude, longitude } = useLocation();

const check = useMutation({
  mutationFn: async () => {
    const response = await axios.post("/api/v1/attendances/check", {
      latitude: latitude.value,
      longitude: longitude.value,
    });

    if (response.status !== 200) throw new Error(response.data.message);

    return response.data.message;
  },
  onSuccess(message) {
    queryClient.invalidateQueries({
      queryKey: ["active", "attendances"],
    });
    // active.refetch();
    // attendances.refetch();

    toast.success(message);
  },
  onError(error: any) {
    toast.error(error.response.data.message);
  },
});

defineProps<{
  isActiveCheckIn: boolean;
}>();
</script>

<template>
  <Button
    size="lg"
    class="rounded-xl"
    :variant="isActiveCheckIn ? 'outline' : 'default'"
    @click="
      () => {
        check.mutate();
      }
    "
  >
    {{ isActiveCheckIn ? "Stämpla ut" : "Stämpla in" }}
  </Button>
</template>
