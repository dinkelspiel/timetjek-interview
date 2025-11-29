<script setup lang="ts">
import type { Attendance } from "@/lib/types";
import { cn } from "@/lib/utils";
import { ref } from "vue";

const props = defineProps<{
  attendances?: Attendance[];
  earliestAttendanceHour: number;
  latestAttendanceHour: number;
  setEarliestAttendanceHour: (a: number) => void;
  setLatestAttendanceHour: (a: number) => void;
  editAttendance: (a: Attendance) => void;
  days: number;
  class?: string;
}>();

const millisecondsInAnHour = 60 * 60 * 1000;

const getWeeksInMilliseconds = (weeks: number): number => {
  return weeks * 24 * millisecondsInAnHour;
};

const getStartOfDay = (day: Date) =>
  new Date(day.getFullYear(), day.getMonth(), day.getDate());
const getEndOfDay = (day: Date) =>
  new Date(day.getFullYear(), day.getMonth(), day.getDate() + 1);

const getAttendancesOnDay = (
  day: Date
): (Attendance & {
  wrap_bottom: boolean;
  wrap_top: boolean;
})[] => {
  if (!props.attendances) {
    return [];
  }

  return props.attendances
    .filter((attendance) => {
      // Filter attendances that don't appear on the day of this element
      const dayStart = getStartOfDay(day);
      const dayEnd = getEndOfDay(day);

      const checkIn = attendance.check_in_at;
      const checkOut = attendance.check_out_at || new Date(8640000000000000); // far future if no checkout yet

      return checkIn < dayEnd && checkOut > dayStart;
    })
    .map((attendance) => {
      if (
        attendance.check_out_at &&
        attendance.check_out_at > getEndOfDay(day)
      ) {
        props.setLatestAttendanceHour(24);
      }

      if (attendance.check_in_at < getStartOfDay(day)) {
        props.setEarliestAttendanceHour(0);
      }

      return {
        ...attendance,
        wrap_bottom: attendance.check_out_at
          ? attendance.check_out_at > getEndOfDay(day)
          : false,
        wrap_top: attendance.check_in_at < getStartOfDay(day),
      };
    });
};

// h-8 in px
const attendanceRowHeight = 32;

const getTopStyleForAttendance = (
  day: Date,
  startHour: number,
  attendance: Attendance
) => {
  const checkIn =
    attendance.check_in_at < getStartOfDay(day)
      ? getStartOfDay(day)
      : attendance.check_in_at;

  const checkInTimeSinceStartOfDayInHours =
    (checkIn.getTime() - getStartOfDay(day).getTime()) / millisecondsInAnHour;
  const hoursSinceStartHour = checkInTimeSinceStartOfDayInHours - startHour;

  const pixelHeight = hoursSinceStartHour * attendanceRowHeight;

  return pixelHeight;
};

const getHeightStyleForAttendance = (
  day: Date,
  startHour: number,
  attendance: Attendance
) => {
  const checkIn =
    attendance.check_in_at < getStartOfDay(day)
      ? getStartOfDay(day)
      : attendance.check_in_at;
  const checkOut = attendance.check_out_at
    ? attendance.check_out_at > getEndOfDay(day)
      ? getEndOfDay(day)
      : attendance.check_out_at
    : new Date();

  const hoursAlive =
    (checkOut.getTime() - checkIn.getTime()) / millisecondsInAnHour;
  const pixelHeight = hoursAlive * attendanceRowHeight;

  return pixelHeight;
};
</script>

<template>
  <div
    v-for="day in Array.from(
      { length: props.days },
      (_, idx) =>
        new Date(
          new Date().getTime() - getWeeksInMilliseconds(props.days - 1 - idx)
        )
    )"
    :class="cn('text-slate-500', props.class)"
  >
    {{
      day.toLocaleDateString("en-US", {
        weekday: "short",
        day: "numeric",
      })
    }}

    <div class="relative pt-2">
      <div
        v-for="_ in Array.from(
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
        class="h-8 border-b-slate-200 border-b"
      ></div>
      <div
        v-for="attendance in getAttendancesOnDay(day)"
        @click="() => editAttendance(attendance)"
        :class="
          cn(
            'absolute mt-2 bg-blue-500 hover:bg-blue-500/90 hover:scale-[101%] hover:z-10 transition-all duration-200 hover:shadow-md cursor-pointer border border-blue-300 shadow-sm p-2 w-full text-sm',
            {
              'rounded-t-lg': !attendance.wrap_top,
              'rounded-b-lg': !attendance.wrap_bottom,
              'py-0 h-4! text-xs':
                getHeightStyleForAttendance(
                  day,
                  earliestAttendanceHour - 1,
                  attendance
                ) < 20,
            }
          )
        "
        :style="{
          top: `${getTopStyleForAttendance(
            day,
            earliestAttendanceHour != 0
              ? earliestAttendanceHour - 1
              : earliestAttendanceHour,
            attendance
          )}px`,
          height: `${getHeightStyleForAttendance(
            day,
            earliestAttendanceHour - 1,
            attendance
          )}px`,
        }"
      >
        <div class="text-white font-semibold" v-if="!attendance.wrap_top">
          {{
            `${attendance.check_in_at
              .getHours()
              .toString()
              .padStart(2, "0")}:${attendance.check_in_at
              .getMinutes()
              .toString()
              .padStart(2, "0")}`
          }}
          -
          {{
            attendance.check_out_at
              ? `${attendance.check_out_at
                  .getHours()
                  .toString()
                  .padStart(2, "0")}:${attendance.check_out_at
                  .getMinutes()
                  .toString()
                  .padStart(2, "0")}`
              : "Now"
          }}
        </div>
      </div>
    </div>
  </div>
</template>
