<template>
  <div class="w-full">
    <div class="flex justify-between mb-2">
      <span class="text-xs sm:text-sm font-medium text-gray-300">{{ label }}</span>
      <span class="text-xs sm:text-sm font-medium" :class="textColorClass">{{ progress }}%</span>
    </div>
    <div class="w-full bg-dark-600 rounded-full h-2.5 overflow-hidden relative">
      <!-- Track glow effect -->
      <div class="absolute inset-0 opacity-20 blur-sm" :class="progressColorClass"></div>

      <!-- Actual progress bar -->
      <div
        class="h-2.5 rounded-full transition-all duration-300 ease-out relative z-10"
        :class="progressColorClass"
        :style="{ width: `${progress}%` }"
      >
        <!-- Leading edge glow -->
        <div
          class="absolute right-0 top-0 bottom-0 w-2 blur-sm"
          :class="progressColorClass"
        ></div>
      </div>
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
      validator: (value: string) => ['primary', 'secondary', 'accent', 'success', 'warning', 'error'].includes(value)
    }
  },
  setup(props) {
    const progressColorClass = computed(() => {
      switch (props.color) {
        case 'secondary':
          return 'bg-cyber-blue';
        case 'accent':
          return 'bg-cyber-purple';
        case 'success':
          return 'bg-cyber-green';
        case 'warning':
          return 'bg-cyber-yellow';
        case 'error':
          return 'bg-cyber-pink';
        default:
          return 'bg-primary-500';
      }
    });

    const textColorClass = computed(() => {
      switch (props.color) {
        case 'secondary':
          return 'text-cyber-blue';
        case 'accent':
          return 'text-cyber-purple';
        case 'success':
          return 'text-cyber-green';
        case 'warning':
          return 'text-cyber-yellow';
        case 'error':
          return 'text-cyber-pink';
        default:
          return 'text-primary-500';
      }
    });

    return {
      progressColorClass,
      textColorClass
    };
  }
});
</script>
