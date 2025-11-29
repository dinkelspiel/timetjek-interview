<script setup lang="ts">
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import DialogFooter from "@/components/ui/dialog/DialogFooter.vue";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { getCsrf } from "@/lib/csrf";
import type { Attendance } from "@/lib/types";
import { useMutation } from "@tanstack/vue-query";
import axios from "axios";
import { ref, watch } from "vue";
import { toast } from "vue-sonner";

const error = ref<string | null>(null);
const reason = ref("");
const checkInAt = ref("");
const checkOutAt = ref("");

const updateAttendance = useMutation({
  mutationFn: async () => {
    if (!props.editAttendance) return;

    const response = await axios.post(
      `/api/v1/attendances/${props.editAttendance.id}`,
      {
        check_in_at: new Date(checkInAt.value).toUTCString(),
        check_out_at: new Date(checkOutAt.value).toUTCString(),
        reason: reason.value,
      }
    );

    return response.data.message;
  },
  onSuccess(message) {
    props.refetch();

    error.value = null;

    toast.success(message);
  },
  onError(err: any) {
    error.value = err.response?.data?.message;

    toast.error(err.response?.data?.message);
  },
});

const props = defineProps<{
  refetch: () => void;
  editAttendance: Attendance | null;
  closeDialog: () => void;
}>();

function toDatetimeLocal(date: Date) {
  const pad = (n: number) => String(n).padStart(2, "0");

  return (
    date.getFullYear() +
    "-" +
    pad(date.getMonth() + 1) +
    "-" +
    pad(date.getDate()) +
    "T" +
    pad(date.getHours()) +
    ":" +
    pad(date.getMinutes())
  );
}

watch(
  () => props.editAttendance,
  (val) => {
    if (!val) return;

    checkInAt.value = toDatetimeLocal(val.check_in_at);
    checkOutAt.value = val.check_out_at
      ? toDatetimeLocal(val.check_out_at)
      : "";
  },
  { immediate: true }
);
</script>

<template>
  <Dialog
    :open="!!editAttendance"
    v-on:update:open="
      () => {
        closeDialog();
      }
    "
  >
    <DialogContent>
      <DialogHeader>
        <DialogTitle> Edit Attendance </DialogTitle>
      </DialogHeader>
      <div class="grid gap-4">
        <div class="grid gap-2">
          <Label>Check in</Label>
          <Input type="datetime-local" v-model="checkInAt" />
        </div>
        <div class="grid gap-2">
          <Label>Check out</Label>
          <Input type="datetime-local" v-model="checkOutAt" />
        </div>
        <div class="grid gap-2">
          <Label>Reason for update</Label>
          <Input v-model="reason" />
        </div>
      </div>
      <InputError v-if="error" :message="error" />
      <DialogFooter>
        <Button
          type="button"
          @click="
            () => {
              updateAttendance.mutate();
            }
          "
        >
          Save
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
