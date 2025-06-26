<template>
    <div class="card hover:shadow-neon-cyber-blue">
        <div class="flex items-center mb-6">
            <div class="h-10 w-10 text-cyber-blue flex items-center justify-center text-2xl rounded-full bg-dark-600 border border-cyber-blue/50">
                <span class="text-glow">🔓</span>
            </div>
            <h2 class="ml-3 text-xl sm:text-2xl font-bold text-white">
                <span class="text-cyber-blue text-glow">Decrypt</span> File
            </h2>
        </div>

        <p class="text-gray-300 mb-6 text-sm sm:text-base">
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
                color="secondary"
            />
        </div>

        <div v-if="processingFile" class="mt-6">
            <div class="flex items-center justify-center space-x-2 text-cyber-blue">
                <div class="animate-spin h-5 w-5 text-center">
                    ⟳
                </div>
                <span class="text-glow">Decrypting file...</span>
            </div>
        </div>

        <button
            @click="decryptFile"
            :disabled="!selectedFile || isProcessing"
            class="btn btn-secondary w-full mt-6 group"
            :class="{ 'opacity-50 cursor-not-allowed': !selectedFile || isProcessing }"
        >
            <span v-if="isProcessing">Processing...</span>
            <span v-else class="group-hover:text-glow transition-all duration-300">Decrypt File</span>
        </button>

        <div class="fixed inset-x-4 bottom-4 z-50 sm:left-auto sm:bottom-6 sm:right-6 sm:w-full sm:max-w-sm">
            <!-- Pass the downloadFilename prop to the Notification component -->
            <Notification
                :show="showNotification"
                :type="notificationType"
                :title="notificationTitle"
                :message="notificationMessage"
                :download-url="downloadUrl"
                :download-filename="downloadFilename"
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
        // Added a ref to hold the filename from the server
        const downloadFilename = ref('');

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
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: (progressEvent) => {
                        if (progressEvent.total) {
                            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            if (uploadProgress.value === 100) processingFile.value = true;
                        }
                    },
                    responseType: 'blob'
                });

                // Get filename from the 'content-disposition' header
                const contentDisposition = response.headers['content-disposition'];
                let filenameFromServer = 'decrypted.txt'; // Fallback
                if (contentDisposition) {
                    const filenameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
                    if (filenameMatch && filenameMatch.length > 1) {
                        filenameFromServer = filenameMatch[1];
                    }
                }

                const url = window.URL.createObjectURL(new Blob([response.data]));
                downloadUrl.value = url;
                downloadFilename.value = filenameFromServer;

                // Show success notification
                notificationType.value = 'success';
                notificationTitle.value = 'File Decrypted Successfully';
                notificationMessage.value = 'Your file has been decrypted and is ready for download.';
                showNotification.value = true;

                uploadProgress.value = 0;
                processingFile.value = false;
            } catch (error: any) {
                console.error('Decryption error:', error);

                let errorMessage = 'There was an error decrypting your file. Please ensure you have the correct key and try again.';
                // If the server sent a specific error message, use it
                if (error.response && error.response.data) {
                    try {
                        const errorBlob = await error.response.data.text();
                        const errorJson = JSON.parse(errorBlob);
                        if (errorJson.error) {
                            errorMessage = errorJson.error;
                        }
                    } catch (e) {
                        // The error response was not JSON, stick with the generic message.
                    }
                }

                notificationType.value = 'error';
                notificationTitle.value = 'Decryption Failed';
                notificationMessage.value = errorMessage;
                showNotification.value = true;

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
            downloadFilename, // Return the ref
            onFileSelected,
            onFileCleared,
            decryptFile
        };
    }
});
</script>
