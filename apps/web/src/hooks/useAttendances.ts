import type { Attendance } from "@/lib/types";
import { useQuery } from "@tanstack/vue-query";
import axios from "axios";
import { ref } from "vue";

export const useAttendances = () => {
  const earliestAttendanceHour = ref(0);
  const latestAttendanceHour = ref(23);
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

  return {
    attendances,
    earliestAttendanceHour,
    latestAttendanceHour,
  };
};
