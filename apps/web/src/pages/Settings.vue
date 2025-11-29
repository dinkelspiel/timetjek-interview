<script setup lang="ts">
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Key, Import, Loader2, LogOut, User } from "lucide-vue-next";
import { computed, ref } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { useRoute, useRouter } from "vue-router";
import {
  Card,
  CardContent,
  CardFooter,
  CardForm,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { useMutation, useQuery } from "@tanstack/vue-query";
import { cn } from "@/lib/utils";
import { useForm } from "@tanstack/vue-form";
import { useAuth } from "@/hooks/useAuth";
import InputError from "@/components/InputError.vue";
import { toast } from "vue-sonner";
import axios from "axios";
import { getCsrf } from "@/lib/csrf";

const route = useRoute();
const router = useRouter();

const logout = useMutation({
  mutationFn: async () => {
    // await getCsrf();
    const response = await axios.get("/api/v1/auth/logout");
    return response;
  },
  onSuccess(res) {
    if (res.status === 200) {
      router.push("/auth/login");
    }
  },
});

const { data: authUser, isPending } = useAuth();
const error = ref<null | string>(null);

const updatePassword = useForm({
  defaultValues: {
    oldPassword: "",
    newPassword: "",
    confirmPassword: "",
  },
  onSubmit: async ({ value }) => {
    // await getCsrf();
    const response = await axios.post("/api/v1/user/password", {
      old_password: value.oldPassword,
      new_password: value.newPassword,
      confirm_password: value.confirmPassword,
    });

    if (response.status !== 200) {
      error.value = response.data.message;
      return;
    }
    error.value = null;
    toast.success(response.data.message);
    updatePassword.setFieldValue("oldPassword", "");
    updatePassword.setFieldValue("newPassword", "");
    updatePassword.setFieldValue("confirmPassword", "");
  },
});
</script>

<template>
  <AppLayout title="Settings">
    <div
      class="xl:gap-8 gap-2 grid grid-rows-[52px_1fr] xl:grid-cols-[min-content_1fr]"
    >
      <div class="w-[250px] flex flex-col gap-4">
        <div class="xl:grid flex gap-2">
          <div class="text-slate-500 hidden xl:block font-semibold text-sm">
            General Settings
          </div>
          <RouterLink to="/settings">
            <Button
              :variant="true ? 'outline' : 'ghost'"
              class="flex justify-start w-full"
            >
              <User class="size-4 stroke-slate-400" />
              Account
            </Button>
          </RouterLink>
          <Button
            :variant="false ? 'outline' : 'ghost'"
            class="flex justify-start w-full"
            @click="
              () => {
                logout.mutate();
              }
            "
          >
            <LogOut class="size-4 stroke-slate-400" />
            Log out
          </Button>
        </div>
      </div>

      <div class="flex flex-col gap-4">
        <h2 class="font-semibold text-2xl leading-9 flex gap-2 items-center">
          Account
          <Loader2
            class="size-4 stroke-slate-400 animate-spin"
            v-if="isPending"
          />
        </h2>

        <div :class="cn('flex flex-col gap-2', { 'opacity-50': isPending })">
          <CardForm
            :class="cn({ 'grayscale pointer-events-none': isPending })"
            @submit.prevent="updatePassword.handleSubmit"
          >
            <input type="hidden" name="institution" value="seb" />

            <CardHeader>
              <CardTitle>
                <span class="me-2">Update Password</span>
              </CardTitle>
            </CardHeader>

            <CardContent>
              <div class="flex flex-col gap-2">
                <Label>Old Password</Label>
                <updatePassword.Field name="oldPassword">
                  <template #default="{ field }">
                    <Input
                      :name="field.name"
                      @update:modelValue="(t) => field.handleChange(t as string)"
                      @blur="field.handleBlur"
                      placeholder="abcd1234abcd1234"
                      autocomplete="off"
                      type="password"
                    />
                  </template>
                </updatePassword.Field>
              </div>
              <div class="grid gap-2 grid-cols-2">
                <div class="flex flex-col gap-2">
                  <Label>New Password</Label>
                  <updatePassword.Field name="newPassword">
                    <template #default="{ field }">
                      <Input
                        :name="field.name"
                        @update:modelValue="(t) => field.handleChange(t as string)"
                        @blur="field.handleBlur"
                        placeholder="abcd1234abcd1234"
                        autocomplete="off"
                        type="password"
                      />
                    </template>
                  </updatePassword.Field>
                </div>
                <div class="flex flex-col gap-2">
                  <Label>Confirm Password</Label>
                  <updatePassword.Field name="confirmPassword">
                    <template #default="{ field }">
                      <Input
                        :name="field.name"
                        @update:modelValue="(t) => field.handleChange(t as string)"
                        @blur="field.handleBlur"
                        placeholder="abcd1234abcd1234"
                        autocomplete="off"
                        type="password"
                      />
                    </template>
                  </updatePassword.Field>
                </div>
              </div>
            </CardContent>
            <InputError v-if="error" :message="error" />
            <CardFooter class="flex gap-2">
              <Button type="submit"> Save </Button>
            </CardFooter>
          </CardForm>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
