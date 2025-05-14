<template>
  <div class="card bg-gradient-to-br from-white to-secondary-50 border border-secondary-100">
    <div class="flex items-center mb-6">
      <div class="h-8 w-8 text-secondary-700 flex items-center justify-center text-2xl">
        🔓
      </div>
      <h2 class="ml-2 text-xl font-bold text-secondary-900">Decrypt File</h2>
    </div>

    <p class="text-secondary-600 mb-6">
      Decrypt your GPG-encrypted files. You'll need the correct key to decrypt the file.
    </p>

    <FileDropZone
      @file-selected="onFileSelected"
      @file-cleared="onFileCleared"
      accept=".gpg"
      accept-text="Only .gpg encrypted files"
    />

    <div v-if="uploadProgress > 0 && uploadProgress < 100" class="mt-6">
      <ProgressBar
        :progress="uploadProgress"
        label="Uploading file..."
      />
    </div>

    <div v-if="processingFile" class="mt-6">
      <div class="flex items-center justify-center space-x-2 text-secondary-700">
        <div class="animate-spin h-5 w-5 text-center">
          ⟳
        </div>
        <span>Decrypting file...</span>
      </div>
    </div>

    <button
      @click="decryptFile"
      :disabled="!selectedFile || isProcessing"
      class="btn btn-secondary w-full mt-6"
      :class="{ 'opacity-50 cursor-not-allowed': !selectedFile || isProcessing }"
    >
      <span v-if="isProcessing">Processing...</span>
      <span v-else>Decrypt File</span>
    </button>

    <div class="fixed bottom-4 right-4 z-50">
      <Notification
        :show="showNotification"
        :type="notificationType"
        :title="notificationTitle"
        :message="notificationMessage"
        :download-url="downloadUrl"
        @close="showNotification = false"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import FileDropZone from './FileDropZone.vue';
import ProgressBar from './ProgressBar.vue';
import Notification from './Notification.vue';
import axios from 'axios';

export default defineComponent({
  name: 'DecryptionPanel',
  components: {
    FileDropZone,
    ProgressBar,
    Notification
  },
  setup() {
    const selectedFile = ref<File | null>(null);
    const uploadProgress = ref(0);
    const processingFile = ref(false);
    const showNotification = ref(false);
    const notificationType = ref('success');
    const notificationTitle = ref('');
    const notificationMessage = ref('');
    const downloadUrl = ref('');

    const isProcessing = computed(() => {
      return uploadProgress.value > 0 || processingFile.value;
    });

    const onFileSelected = (file: File) => {
      selectedFile.value = file;
      uploadProgress.value = 0;
      showNotification.value = false;
    };

    const onFileCleared = () => {
      selectedFile.value = null;
      uploadProgress.value = 0;
      showNotification.value = false;
    };

    const decryptFile = async () => {
      if (!selectedFile.value || isProcessing.value) return;

      try {
        const formData = new FormData();
        formData.append('file_to_decrypt', selectedFile.value);

        uploadProgress.value = 0;
        processingFile.value = false;

        const response = await axios.post('/decrypt', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (progressEvent) => {
            if (progressEvent.total) {
              uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);

              if (uploadProgress.value === 100) {
                processingFile.value = true;
              }
            }
          },
          responseType: 'blob'
        });

        // Create a download link for the decrypted file
        const url = window.URL.createObjectURL(new Blob([response.data]));
        downloadUrl.value = url;

        // Show success notification
        notificationType.value = 'success';
        notificationTitle.value = 'File Decrypted Successfully';
        notificationMessage.value = 'Your file has been decrypted and is ready for download.';
        showNotification.value = true;

        // Reset progress indicators
        uploadProgress.value = 0;
        processingFile.value = false;
      } catch (error) {
        console.error('Decryption error:', error);

        // Show error notification
        notificationType.value = 'error';
        notificationTitle.value = 'Decryption Failed';
        notificationMessage.value = 'There was an error decrypting your file. Please ensure you have the correct key and try again.';
        showNotification.value = true;

        // Reset progress indicators
        uploadProgress.value = 0;
        processingFile.value = false;
      }
    };

    return {
      selectedFile,
      uploadProgress,
      processingFile,
      isProcessing,
      showNotification,
      notificationType,
      notificationTitle,
      notificationMessage,
      downloadUrl,
      onFileSelected,
      onFileCleared,
      decryptFile
    };
  }
});
</script>
