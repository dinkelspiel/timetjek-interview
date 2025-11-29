<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import AuthLayout from "@/layouts/AuthLayout.vue";
import TextLink from "@/components/TextLink.vue";
import axios from "axios";
import { getCsrf } from "@/lib/csrf";

// defineProps<{
//     status?: string;
//     canResetPassword: boolean;
//     canRegister: boolean;
// }>();

const errors = ref<string | null>(null);

function login(event: SubmitEvent) {
  event.preventDefault();

  getCsrf().then(() => {
    axios
      .post(
        "/api/v1/auth/signup",
        {
          firstname: (event.target as HTMLFormElement).firstname.value,
          lastname: (event.target as HTMLFormElement).lastname.value,
          pin: `${(event.target as HTMLFormElement).birthdate.value}${
            (event.target as HTMLFormElement).control.value
          }`,
          email: (event.target as HTMLFormElement).email.value,
          password: (event.target as HTMLFormElement).password.value,
          remember: (event.target as HTMLFormElement).remember.checked,
        },
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
      )
      .then((response) => {
        console.log("logged in", response);
        window.location.href = "/overview";
      })
      .catch((error) => {
        errors.value = error.response.data.message;
      });
  });
}

import { ref } from "vue";
import InputError from "@/components/InputError.vue";

const birthdate = ref("");
const email = ref<HTMLInputElement | null>(null);
const control = ref<HTMLInputElement | null>(null);
const controlValue = ref("");

function checkBirthdateLength() {
  if (birthdate.value.length === 6) {
    control.value?.focus();
  }
}

function checkControlLength() {
  if (controlValue.value.length === 4) {
    email.value?.focus();
  }
}
</script>

<template>
  <AuthLayout
    title="Register your account"
    description="Enter your name, email and password below to register"
  >
    <Head title="Sign up" />

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
        <div class="grid gap-2">
          <Label for="email">First Name</Label>
          <Input
            id="firstname"
            type="text"
            name="firstname"
            required
            autofocus
            :tabindex="1"
            autocomplete="given-name"
            placeholder="John"
          />
          <!-- <InputError :message="errors.firstname" /> -->
        </div>

        <div class="grid gap-2">
          <Label for="email">Last Name</Label>
          <Input
            id="lastname"
            type="text"
            name="lastname"
            required
            :tabindex="1"
            autocomplete="family-name"
            placeholder="Smith"
          />
          <!-- <InputError :message="errors.email" /> -->
        </div>

        <div class="grid gap-2">
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
          <Label for="email">Email address</Label>
          <Input
            id="email"
            type="email"
            name="email"
            ref="email"
            required
            :tabindex="2"
            autocomplete="email"
            placeholder="email@example.com"
          />
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
            required
            :tabindex="3"
            autocomplete="current-password"
            placeholder="Password"
          />
          <!-- <InputError :message="errors.password" /> -->
        </div>

        <InputError v-if="errors" :message="errors" />

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
          Sign up
        </Button>
      </div>

      <div class="text-center text-sm text-muted-foreground">
        Already have an account?
        <TextLink href="/auth/login" :tabindex="5">Log in</TextLink>
      </div>
    </form>
  </AuthLayout>
</template>
