<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { ref } from "vue";
import Card from "@/components/ui/card/Card.vue";
import CardHeader from "@/components/ui/card/CardHeader.vue";
import CardTitle from "@/components/ui/card/CardTitle.vue";
import { useMutation, useQuery, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import CardContent from "@/components/ui/card/CardContent.vue";
import EditAttendance from "./EditAttendance.vue";
import type { Attendance } from "@/lib/types";
import axios from "axios";
import AttendanceDay from "./AttendanceDay.vue";
import { useAttendances } from "@/hooks/useAttendances";
import { useLocation } from "@/hooks/useLocation";
import CheckInToggleButton from "./CheckInToggleButton.vue";

const editAttendance = ref<Attendance | null>(null);
const editAttendanceReason = ref<string>("");
const queryClient = useQueryClient();

const active = useQuery({
  queryKey: ["active"],
  queryFn: async () => {
    const response = await axios.get("/api/v1/attendances/active");

    if (response.status !== 200) throw new Error(response.data.message);

    isActiveCheckIn.value = !!response.data.data;
    return response.data;
  },
});

const { attendances, earliestAttendanceHour, latestAttendanceHour } =
  useAttendances();
const { hasGeolocation } = useLocation();

const isActiveCheckIn = ref(false);
</script>

<template>
  <Card class="rounded-4xl col-span-2">
    <CardHeader class="flex justify-between w-full">
      <CardTitle> Calendar </CardTitle>
      <div class="flex gap-4 items-center">
        <div v-if="!hasGeolocation" class="text-slate-500 text-sm">
          Geolocation is not supported by this browser.
        </div>
        <CheckInToggleButton :is-active-check-in="isActiveCheckIn" />
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
        <!-- 7 Days for desktop but only 4 days for mobile -->
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
