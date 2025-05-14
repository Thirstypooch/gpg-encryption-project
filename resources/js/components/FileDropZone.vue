<template>
  <div
    class="drop-zone"
    :class="{ 'drop-zone-active': isDragging }"
    @dragenter.prevent="isDragging = true"
    @dragover.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @drop.prevent="onDrop"
    @click="triggerFileInput"
  >
    <input
      type="file"
      ref="fileInput"
      class="hidden"
      @change="onFileSelected"
      :accept="accept"
    />

    <div v-if="!selectedFile" class="py-4">
      <div class="mx-auto h-14 w-14 text-cyber-blue flex items-center justify-center text-3xl mb-2">
        <span class="text-glow">↑</span>
      </div>
      <p class="mt-2 text-sm sm:text-base text-gray-300">
        <span class="font-medium text-cyber-blue hover:text-glow transition-all duration-300">
          Upload a file
        </span>
        or drag and drop
      </p>
      <p class="mt-1 text-xs sm:text-sm text-gray-400">
        {{ acceptText }}
      </p>
    </div>

    <div v-else class="text-left">
      <div class="flex items-center">
        <div class="h-10 w-10 text-cyber-purple flex items-center justify-center text-xl bg-dark-600 rounded-md border border-cyber-purple/30">
          <span class="text-glow">📄</span>
        </div>
        <div class="ml-4">
          <h4 class="text-sm sm:text-base font-medium text-white">
            {{ selectedFile.name }}
          </h4>
          <p class="text-xs sm:text-sm text-gray-400">
            {{ formatFileSize(selectedFile.size) }}
          </p>
        </div>
        <button
          @click.stop="clearFile"
          class="ml-auto p-1 rounded-full text-gray-400 hover:bg-dark-600 hover:text-cyber-pink focus:outline-none transition-colors duration-300"
        >
          <div class="h-6 w-6 flex items-center justify-center">
            ✕
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'FileDropZone',
  props: {
    accept: {
      type: String,
      default: '*'
    },
    acceptText: {
      type: String,
      default: 'Any file type'
    }
  },
  emits: ['file-selected', 'file-cleared'],
  setup(props, { emit }) {
    const fileInput = ref<HTMLInputElement | null>(null);
    const selectedFile = ref<File | null>(null);
    const isDragging = ref(false);

    const triggerFileInput = () => {
      if (fileInput.value) {
        fileInput.value.click();
      }
    };

    const onFileSelected = (event: Event) => {
      const input = event.target as HTMLInputElement;
      if (input.files && input.files.length > 0) {
        selectedFile.value = input.files[0];
        emit('file-selected', selectedFile.value);
      }
    };

    const onDrop = (event: DragEvent) => {
      isDragging.value = false;
      if (event.dataTransfer?.files.length) {
        selectedFile.value = event.dataTransfer.files[0];
        emit('file-selected', selectedFile.value);
      }
    };

    const clearFile = (event: Event) => {
      event.stopPropagation();
      selectedFile.value = null;
      if (fileInput.value) {
        fileInput.value.value = '';
      }
      emit('file-cleared');
    };

    const formatFileSize = (bytes: number): string => {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    return {
      fileInput,
      selectedFile,
      isDragging,
      triggerFileInput,
      onFileSelected,
      onDrop,
      clearFile,
      formatFileSize
    };
  }
});
</script>
