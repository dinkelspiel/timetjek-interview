<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { House, HandCoins, Bell, DollarSign } from "lucide-vue-next";
import { ref } from "vue";
import { onMounted } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import Card from "@/components/ui/card/Card.vue";
import CardHeader from "@/components/ui/card/CardHeader.vue";
import CardTitle from "@/components/ui/card/CardTitle.vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import CardContent from "@/components/ui/card/CardContent.vue";
import { cn } from "@/lib/utils";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import EditAttendance from "./EditAttendance.vue";
import type { Attendance } from "@/lib/types";
import axios from "axios";
import { getCsrf } from "@/lib/csrf";
import AttendanceDay from "./AttendanceDay.vue";
import Attendances from "./Attendances.vue";

const editAttendance = ref<Attendance | null>(null);
const editAttendanceReason = ref<string>("");
const queryClient = useQueryClient();

const check = useMutation({
  mutationFn: async () => {
    // await getCsrf();
    const response = await axios.post("/api/v1/attendances/check", {
      latitude: latitude.value,
      longitude: longitude.value,
    });

    if (response.status !== 200) throw new Error(response.data.message);

    return response.data.message;
  },
  onSuccess(message) {
    active.refetch();
    attendances.refetch();

    toast.success(message);
  },
  onError(error: any) {
    toast.error(error.response.data.message);
  },
});

const active = useQuery({
  queryKey: ["active"],
  queryFn: async () => {
    // await getCsrf();
    const response = await axios.get("/api/v1/attendances/active");

    if (response.status !== 200) throw new Error(response.data.message);

    isActiveCheckIn.value = !!response.data.data;
    return response.data;
  },
});

const attendances = useQuery({
  queryKey: ["attendances"],
  queryFn: async () => {
    // await getCsrf();
    const response = await axios.get(`/api/v1/attendances`);

    const data: {
      message: string;
      data: Attendance[];
    } = response.data;

    if (response.status !== 200) throw new Error(data.message);

    let earliestAttendance = 23;
    let latestAttendance = 0;

    // Map string dates from api to Date objects
    data.data = data.data.map((e: Attendance) => ({
      ...e,
      check_in_at: new Date(e.check_in_at),
      check_out_at: e.check_out_at ? new Date(e.check_out_at) : null,
    }));

    // Find the earliest and latest hours the person has worked so the UI doesn't show 0 - 24 for all users
    data.data.forEach((attendance: Attendance) => {
      if (attendance.check_in_at.getHours() < earliestAttendance) {
        earliestAttendance = attendance.check_in_at.getHours();
      }
      if (
        attendance.check_out_at &&
        attendance.check_out_at.getHours() > latestAttendance
      ) {
        latestAttendance = attendance.check_out_at.getHours();
      }

      if (
        !attendance.check_out_at &&
        new Date().getHours() > latestAttendance
      ) {
        latestAttendance = new Date().getHours();
      }
    });

    // If no values are found use sane defaults (9 - 17)
    if (earliestAttendance != 23) {
      earliestAttendanceHour.value = earliestAttendance;
    } else {
      earliestAttendanceHour.value = 9;
    }

    if (latestAttendance != 0) {
      latestAttendanceHour.value = latestAttendance;
    } else {
      latestAttendanceHour.value = 17;
    }

    return data;
  },
});

const isActiveCheckIn = ref(false);

const hasGeolocation = ref(false);
const latitude = ref<number | null>(null);
const longitude = ref<number | null>(null);

const earliestAttendanceHour = ref(0);
const latestAttendanceHour = ref(23);

onMounted(() => {
  navigator.geolocation.getCurrentPosition(
    async (position) => {
      latitude.value = position.coords.latitude;
      longitude.value = position.coords.longitude;
      hasGeolocation.value = true;
      console.log("asd", longitude.value, latitude.value);
    },
    (geoError) => {},
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0,
    }
  );
});
</script>

<template>
  <Card class="rounded-4xl col-span-2">
    <CardHeader class="flex justify-between w-full">
      <CardTitle> Calendar </CardTitle>
      <div class="flex gap-4 items-center">
        <div v-if="!hasGeolocation" class="text-slate-500 text-sm">
          Geolocation is not supported by this browser.
        </div>
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
      </div>
    </CardHeader>
    <CardContent>
      <div
        class="grid grid-cols-[50px_1fr_1fr_1fr_1fr] xl:grid-cols-[50px_1fr_1fr_1fr_1fr_1fr_1fr_1fr]"
      >
        <div>
          <div class="text-slate-500 flex items-start pb-2">
            {{
              new Date().toLocaleDateString("en-US", {
                month: "short",
              })
            }}
          </div>
          <div
            v-for="hour in Array.from(
              {
                length:
                  (latestAttendanceHour != 24
                    ? latestAttendanceHour + 1
                    : latestAttendanceHour) -
                  (earliestAttendanceHour != 0
                    ? earliestAttendanceHour - 1
                    : earliestAttendanceHour),
              },
              (_, idx) => idx + earliestAttendanceHour
            )"
            class="h-8 text-slate-500 justify-end text-xs pe-4 flex items-start border-b border-b-slate-200 pt-1"
          >
            {{ hour.toString().padStart(2, "0") }}:00
          </div>
        </div>
        <AttendanceDay
          :days="7"
          class="hidden xl:block"
          :attendances="attendances.data?.value?.data"
          :earliest-attendance-hour="earliestAttendanceHour"
          :latest-attendance-hour="latestAttendanceHour"
          :set-earliest-attendance-hour="(a) => (earliestAttendanceHour = a)"
          :set-latest-attendance-hour="(a) => (latestAttendanceHour = a)"
          :edit-attendance="
            (attendance) => {
              editAttendance = attendance;
              editAttendanceReason = '';
            }
          "
        />
        <AttendanceDay
          :days="4"
          class="block xl:hidden"
          :attendances="attendances.data?.value?.data"
          :earliest-attendance-hour="earliestAttendanceHour"
          :latest-attendance-hour="latestAttendanceHour"
          :set-earliest-attendance-hour="(a) => (earliestAttendanceHour = a)"
          :set-latest-attendance-hour="(a) => (latestAttendanceHour = a)"
          :edit-attendance="
            (attendance) => {
              editAttendance = attendance;
              editAttendanceReason = '';
            }
          "
        />
      </div>
    </CardContent>
  </Card>
  <EditAttendance
    :refetch="
      () => {
        active.refetch();
        attendances.refetch();
        queryClient.invalidateQueries({
          queryKey: ['logs'],
        });
      }
    "
    :edit-attendance="editAttendance"
    :close-dialog="
      () => {
        editAttendance = null;
      }
    "
  />
</template>
