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

    <div v-if="!selectedFile">
      <svg class="mx-auto h-12 w-12 text-secondary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
      </svg>
      <p class="mt-2 text-sm text-secondary-600">
        <span class="font-medium text-primary-600 hover:text-primary-500">
          Upload a file
        </span>
        or drag and drop
      </p>
      <p class="mt-1 text-xs text-secondary-500">
        {{ acceptText }}
      </p>
    </div>

    <div v-else class="text-left">
      <div class="flex items-center">
        <svg class="h-8 w-8 text-secondary-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <div class="ml-4">
          <h4 class="text-sm font-medium text-secondary-900">
            {{ selectedFile.name }}
          </h4>
          <p class="text-xs text-secondary-500">
            {{ formatFileSize(selectedFile.size) }}
          </p>
        </div>
        <button
          @click.stop="clearFile"
          class="ml-auto p-1 rounded-full text-secondary-500 hover:bg-secondary-100 hover:text-secondary-700 focus:outline-none"
        >
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
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
