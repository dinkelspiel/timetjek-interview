<script setup lang="ts">
import { ref, computed, type HTMLAttributes } from "vue";
import { cn } from "@/lib/utils";
import { useVModel } from "@vueuse/core";

const props = defineProps<{
  defaultValue?: string | number;
  value?: string | number;
  modelValue?: string | number;
  class?: HTMLAttributes["class"];
}>();

const emits = defineEmits<{
  (e: "update:modelValue", payload: string | number): void;
}>();

// This is the v-model binding
const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
});

const inputRef = ref<HTMLInputElement | null>(null);

// Prefer modelValue if it's used, otherwise fall back to value
const inputValue = computed({
  get() {
    // Priority: modelValue -> value -> defaultValue -> ""
    if (props.modelValue !== undefined) return modelValue.value;
    if (props.value !== undefined) return props.value;
    return props.defaultValue ?? "";
  },
  set(val: string | number) {
    // Only emit when using v-model. If someone is using plain `value`,
    // this will still emit (so they *can* listen), but the source of truth
    // remains in the parent.
    emits("update:modelValue", val);
  },
});

defineExpose<{
  focus: () => void;
  inputRef: typeof inputRef;
}>({
  focus: () => {
    inputRef.value?.focus();
  },
  inputRef,
});
</script>

<template>
  <input
    ref="inputRef"
    v-model="inputValue"
    data-slot="input"
    :class="
      cn(
        'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-white px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
        'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
        'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
        props.class
      )
    "
  />
</template>
