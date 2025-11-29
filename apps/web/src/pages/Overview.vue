<script setup lang="ts">
import AppLayout from "@/layouts/AppLayout.vue";
import Attendances from "./Overview/Attendances.vue";
import UpdateLog from "./Overview/UpdateLog.vue";
</script>
<template>
  <AppLayout title="Overview">
    <div class="grid grid-cols-2 gap-4 h-fit">
      <Attendances />
      <UpdateLog />
    </div>
  </AppLayout>
</template>
