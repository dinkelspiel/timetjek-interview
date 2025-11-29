<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthLayout from "@/layouts/AuthLayout.vue";
import { LoaderCircle, Mail, Shield } from "lucide-vue-next";
import TextLink from "@/components/TextLink.vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { ref } from "vue";
import InputError from "@/components/InputError.vue";
import { getCsrf } from "@/lib/csrf";

// defineProps<{
//     status?: string;
//     canResetPassword: boolean;
//     canRegister: boolean;
// }>();

const router = useRouter();
const mode = ref<"email" | "pin">("email");

function login(event: SubmitEvent) {
  event.preventDefault();
  // getCsrf().then(() => {
  axios
    .post(
      "/api/v1/auth/login",
      {
        email_or_pin:
          mode.value === "email"
            ? (event.target as HTMLFormElement).email.value
            : `${(event.target as HTMLFormElement).birthdate.value}${
                (event.target as HTMLFormElement).control.value
              }`,
        password: (event.target as HTMLFormElement).password.value,
        mode: mode.value,
        remember: (event.target as HTMLFormElement).remember.checked,
      },
      {
        headers: {
          Accept: "application/json",
        },
      }
    )
    .then((response) => {
      console.log("logged in", response);
      router.push({ name: "overview" });
    })
    .catch(() => {
      error.value = "The provided credentials are incorrect.";
    });
  // });
}

const error = ref<null | string>(null);
const birthdate = ref("");
const password = ref<HTMLInputElement | null>(null);
const control = ref<HTMLInputElement | null>(null);
const controlValue = ref("");

function checkBirthdateLength() {
  if (birthdate.value.length === 6) {
    control.value?.focus();
  }
}

function checkControlLength() {
  if (controlValue.value.length === 4) {
    password.value?.focus();
  }
}
</script>

<template>
  <AuthLayout
    title="Log in to your account"
    description="Enter your email and password below to log in"
  >
    <!-- <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div> -->

    <form
      :reset-on-success="['password']"
      @submit="login"
      class="flex flex-col gap-6"
    >
      <div class="grid gap-6">
        <div class="rounded-xl bg-slate-200 w-full p-1 grid-cols-2 grid gap-1">
          <Button
            class="w-full"
            :variant="mode === 'email' ? 'outline' : 'ghost'"
            @click="
              () => {
                mode = 'email';
              }
            "
            ><Mail class="size-4 stroke-slate-500" /> Email</Button
          >
          <Button
            class="w-full"
            :variant="mode === 'pin' ? 'outline' : 'ghost'"
            @click="
              () => {
                mode = 'pin';
              }
            "
            ><Shield class="size-4 stroke-slate-500" /> Personal Id
            Number</Button
          >
        </div>
        <div class="grid gap-2" v-if="mode === 'email'">
          <Label for="email">Email</Label>
          <Input
            id="email"
            type="email"
            name="email"
            required
            autofocus
            :tabindex="1"
            autocomplete="email"
            placeholder="email@example.com"
          />
          <!-- <InputError :message="errors.email" /> -->
        </div>
        <div class="grid gap-2" v-if="mode === 'pin'">
          <Label for="email">Personal Identity Number (Personnummer)</Label>
          <div class="flex gap-1.5">
            <Input
              id="birthdate"
              type="text"
              name="birthdate"
              required
              autofocus
              :tabindex="1"
              autocomplete="text"
              placeholder="890201"
              v-model="birthdate"
              @input="checkBirthdateLength"
            />
            <div class="text-slate-400 flex items-center">-</div>
            <div>
              <Input
                id="control"
                ref="control"
                type="text"
                name="control"
                required
                :tabindex="1"
                placeholder="3286"
                v-model="controlValue"
                @input="checkControlLength"
              />
            </div>
          </div>
          <!-- <InputError :message="errors.email" /> -->
        </div>

        <div class="grid gap-2">
          <div class="flex items-center justify-between">
            <Label for="password">Password</Label>
            <!-- <TextLink
                            v-if="canResetPassword"
                            href="/auth/forgot-password"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink> -->
          </div>
          <Input
            id="password"
            type="password"
            name="password"
            ref="password"
            required
            :tabindex="2"
            autocomplete="current-password"
            placeholder="Password"
          />
        </div>
        <InputError v-if="error" :message="error" />

        <div class="flex items-center justify-between">
          <Label for="remember" class="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" :tabindex="3" />
            <span>Remember me</span>
          </Label>
        </div>

        <Button
          type="submit"
          class="mt-4 w-full"
          :tabindex="4"
          data-test="login-button"
        >
          <!-- <LoaderCircle class="h-4 w-4 animate-spin" /> -->
          Log in
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Don't have an account?
        <TextLink href="/auth/signup">Sign up</TextLink>
      </div>
    </form>
  </AuthLayout>
</template>
