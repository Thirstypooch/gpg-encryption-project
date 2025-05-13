<template>
  <div class="w-full">
    <div class="flex justify-between mb-1">
      <span class="text-sm font-medium text-secondary-700">{{ label }}</span>
      <span class="text-sm font-medium text-secondary-700">{{ progress }}%</span>
    </div>
    <div class="w-full bg-secondary-200 rounded-full h-2.5">
      <div
        class="h-2.5 rounded-full transition-all duration-300 ease-out"
        :class="progressColorClass"
        :style="{ width: `${progress}%` }"
      ></div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ProgressBar',
  props: {
    progress: {
      type: Number,
      required: true,
      validator: (value: number) => value >= 0 && value <= 100
    },
    label: {
      type: String,
      default: 'Progress'
    },
    color: {
      type: String,
      default: 'primary',
      validator: (value: string) => ['primary', 'success', 'warning', 'error'].includes(value)
    }
  },
  setup(props) {
    const progressColorClass = computed(() => {
      switch (props.color) {
        case 'success':
          return 'bg-green-600';
        case 'warning':
          return 'bg-yellow-500';
        case 'error':
          return 'bg-red-600';
        default:
          return 'bg-primary-600';
      }
    });

    return {
      progressColorClass
    };
  }
});
</script>
