<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useAuth } from "@/hooks/useAuth";
import { cn } from "@/lib/utils";
import Settings from "@/pages/Settings.vue";
import { useQuery } from "@tanstack/vue-query";
import {
  House,
  HandCoins,
  Bell,
  DollarSign,
  UsersRound,
  Cog,
  Calendar,
} from "lucide-vue-next";
import { ref, useId } from "vue";
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const accounts = ref<{ id: number; name: string }[]>([]);

const route = useRoute();
const router = useRouter();

const { data: authUser } = useAuth();

const props = defineProps<{
  title?: string;
}>();
</script>
<template>
  <div class="bg-slate-100">
    <div
      class="min-[1368px]:w-[1368px] mx-auto min-h-dvh p-4 xl:p-8 gap-8 grid grid-rows-[52px_1fr] xl:grid-rows-1 xl:grid-cols-[min-content_1fr]"
    >
      <div class="w-[250px] flex h-fit gap-4 xl:grid items-center">
        <div
          class="h-12 text-slate-700 font-bold rounded-md px-3 py-1.5 items-center flex justify-center gap-4"
        >
          <Calendar class="size-4 text-slate-400" />
          Timetjek
        </div>
        <div class="xl:grid flex gap-2">
          <div class="text-slate-500 font-semibold text-sm xl:block hidden">
            General
          </div>
          <RouterLink to="/overview">
            <Button
              :variant="route.name === 'overview' ? 'outline' : 'ghost'"
              class="flex justify-start w-full"
            >
              <House class="size-4 stroke-slate-400" />
              Overview
            </Button>
          </RouterLink>
          <RouterLink to="/settings">
            <Button
              :variant="route.name === 'settings' ? 'outline' : 'ghost'"
              class="flex justify-start w-full"
            >
              <Cog class="size-4 stroke-slate-400 -scale-y-100" />
              Settings
            </Button>
          </RouterLink>
        </div>
      </div>
      <div class="flex flex-col gap-4">
        <div class="h-12 flex justify-between items-center">
          <h1 class="font-semibold text-[36px] leading-9">
            {{ props.title }}
          </h1>
          <div class="flex gap-4">
            <!-- <Button
              variant="outline"
              class="size-12 rounded-full flex items-center justify-center"
            >
              <Bell class="stroke-slate-500 size-4" />
            </Button> -->
            <RouterLink to="/settings">
              <Button
                variant="outline"
                class="rounded-full h-12 pe-4 ps-2 flex gap-4 items-center"
              >
                <div class="rounded-full size-8 bg-slate-100"></div>
                <div class="font-semibold">
                  {{ authUser?.firstname }} {{ authUser?.lastname[0] }}
                </div>
              </Button>
            </RouterLink>
          </div>
        </div>
        <slot></slot>
      </div>
    </div>
  </div>
</template>
