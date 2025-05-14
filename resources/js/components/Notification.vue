<template>
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <!-- Success Icon -->
            <div v-if="type === 'success'" class="h-6 w-6 text-green-400 flex items-center justify-center text-xl">
              ✓
            </div>
            <!-- Error Icon -->
            <div v-else-if="type === 'error'" class="h-6 w-6 text-red-400 flex items-center justify-center text-xl">
              ⚠
            </div>
            <!-- Info Icon -->
            <div v-else class="h-6 w-6 text-blue-400 flex items-center justify-center text-xl">
              ℹ
            </div>
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium text-secondary-900">{{ title }}</p>
            <p v-if="message" class="mt-1 text-sm text-secondary-500">{{ message }}</p>
            <div v-if="downloadUrl" class="mt-3">
              <a
                :href="downloadUrl"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                Download File
              </a>
            </div>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button
              @click="$emit('close')"
              class="bg-white rounded-md inline-flex text-secondary-400 hover:text-secondary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <span class="sr-only">Close</span>
              <div class="h-5 w-5 flex items-center justify-center">
                ✕
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Notification',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'info',
      validator: (value: string) => ['success', 'error', 'info'].includes(value)
    },
    title: {
      type: String,
      required: true
    },
    message: {
      type: String,
      default: ''
    },
    downloadUrl: {
      type: String,
      default: ''
    }
  },
  emits: ['close']
});
</script>
