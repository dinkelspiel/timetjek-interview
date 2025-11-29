<script setup lang="ts">
import { RouterLink, RouterView } from "vue-router";
import "vue-sonner/style.css";
import { Toaster } from "vue-sonner";
import { onMounted } from "vue";
import axios from "axios";
</script>

<template>
  <Toaster />
  <RouterView />
</template>
