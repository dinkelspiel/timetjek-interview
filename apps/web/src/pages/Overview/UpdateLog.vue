<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useQuery } from "@tanstack/vue-query";
import axios from "axios";

const logs = useQuery<{
  message: string;
  data: {
    id: number;
    message: string;
    created_at: string;
  }[];
}>({
  queryKey: ["logs"],
  queryFn: async () => {
    const response = await axios.get("/api/v1/user/logs");
    return response.data;
  },
});
</script>
<template>
  <Card class="rounded-4xl col-span-2">
    <CardHeader class="flex justify-between w-full">
      <CardTitle> Update log </CardTitle>
    </CardHeader>
    <CardContent>
      <div
        v-if="logs.data.value"
        v-for="log in logs.data.value.data"
        :key="log.id"
        class="text-sm flex justify-between gap-4"
      >
        <div>
          {{ log.message }}
        </div>

        <div class="text-slate-500 w-[150px] text-right">
          {{ new Date(log.created_at).toDateString() }}
        </div>
      </div>
    </CardContent>
  </Card>
</template>
