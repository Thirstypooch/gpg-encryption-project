<template>
  <div class="card bg-gradient-to-br from-white to-primary-50 border border-primary-100">
    <div class="flex items-center mb-6">
      <svg class="h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
      </svg>
      <h2 class="ml-2 text-xl font-bold text-secondary-900">Encrypt File</h2>
    </div>

    <p class="text-secondary-600 mb-6">
      Securely encrypt your files using GPG encryption. The encrypted file can only be decrypted with the correct key.
    </p>

    <FileDropZone
      @file-selected="onFileSelected"
      @file-cleared="onFileCleared"
      accept-text="Any file type can be encrypted"
    />

    <div v-if="uploadProgress > 0 && uploadProgress < 100" class="mt-6">
      <ProgressBar
        :progress="uploadProgress"
        label="Uploading file..."
      />
    </div>

    <div v-if="processingFile" class="mt-6">
      <div class="flex items-center justify-center space-x-2 text-primary-700">
        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Encrypting file...</span>
      </div>
    </div>

    <button
      @click="encryptFile"
      :disabled="!selectedFile || isProcessing"
      class="btn btn-primary w-full mt-6"
      :class="{ 'opacity-50 cursor-not-allowed': !selectedFile || isProcessing }"
    >
      <span v-if="isProcessing">Processing...</span>
      <span v-else>Encrypt File</span>
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
  name: 'EncryptionPanel',
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

    const encryptFile = async () => {
      if (!selectedFile.value || isProcessing.value) return;

      try {
        const formData = new FormData();
        formData.append('file_to_encrypt', selectedFile.value);

        uploadProgress.value = 0;
        processingFile.value = false;

        const response = await axios.post('/encrypt', formData, {
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

        // Create a download link for the encrypted file
        const url = window.URL.createObjectURL(new Blob([response.data]));
        downloadUrl.value = url;

        // Show success notification
        notificationType.value = 'success';
        notificationTitle.value = 'File Encrypted Successfully';
        notificationMessage.value = 'Your file has been encrypted and is ready for download.';
        showNotification.value = true;

        // Reset progress indicators
        uploadProgress.value = 0;
        processingFile.value = false;
      } catch (error) {
        console.error('Encryption error:', error);

        // Show error notification
        notificationType.value = 'error';
        notificationTitle.value = 'Encryption Failed';
        notificationMessage.value = 'There was an error encrypting your file. Please try again.';
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
      encryptFile
    };
  }
});
</script>
