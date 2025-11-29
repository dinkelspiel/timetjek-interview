<script setup lang="ts">
defineProps<{
  message: string;
}>();
</script>
<template>
  <div class="text-sm text-center text-red-500">
    {{ $props.message }}
  </div>
</template>
