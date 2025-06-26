<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="w-full backdrop-blur-md bg-dark-700/90 shadow-lg rounded-lg pointer-events-auto overflow-hidden border"
            :class="notificationBorderClass"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <div
                            v-if="type === 'success'"
                            class="h-8 w-8 flex items-center justify-center text-xl rounded-full bg-dark-600 border"
                            :class="['text-cyber-green text-glow', 'border-cyber-green/30']"
                        >
                            ✓
                        </div>
                        <!-- Error Icon -->
                        <div
                            v-else-if="type === 'error'"
                            class="h-8 w-8 flex items-center justify-center text-xl rounded-full bg-dark-600 border"
                            :class="['text-cyber-pink text-glow', 'border-cyber-pink/30']"
                        >
                            ⚠
                        </div>
                        <!-- Info Icon -->
                        <div
                            v-else
                            class="h-8 w-8 flex items-center justify-center text-xl rounded-full bg-dark-600 border"
                            :class="['text-cyber-blue text-glow', 'border-cyber-blue/30']"
                        >
                            ℹ
                        </div>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm sm:text-base font-medium text-white">{{ title }}</p>
                        <p v-if="message" class="mt-1 text-xs sm:text-sm text-gray-300">{{ message }}</p>
                        <div v-if="downloadUrl" class="mt-4">
                            <!-- The 'download' attribute is now bound to the filename prop -->
                            <a
                                :href="downloadUrl"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md shadow-sm text-white bg-cyber-blue hover:bg-cyber-blue/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyber-blue transition-all duration-300 hover:shadow-neon-cyber-blue"
                                :download="downloadFilename"
                            >
                                Download File
                            </a>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            @click="$emit('close')"
                            class="rounded-md inline-flex text-gray-400 hover:text-white focus:outline-none transition-colors duration-300"
                        >
                            <span class="sr-only">Close</span>
                            <div class="h-6 w-6 flex items-center justify-center">
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
import { defineComponent, computed } from 'vue';

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
        },
        downloadFilename: {
            type: String,
            default: ''
        }
    },
    setup(props) {
        const notificationBorderClass = computed(() => {
            switch (props.type) {
                case 'success':
                    return 'border-cyber-green/50 shadow-neon-cyber-green';
                case 'error':
                    return 'border-cyber-pink/50 shadow-neon-cyber-pink';
                default:
                    return 'border-cyber-blue/50 shadow-neon-cyber-blue';
            }
        });

        return {
            notificationBorderClass
        };
    },
    emits: ['close']
});
</script>
