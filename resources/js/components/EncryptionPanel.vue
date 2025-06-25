<template>
    <div class="card hover:shadow-neon-cyber-green">
        <div class="flex items-center mb-6">
            <div class="h-10 w-10 text-cyber-green flex items-center justify-center text-2xl rounded-full bg-dark-600 border border-cyber-green/50">
                <span class="text-glow">🔒</span>
            </div>
            <h2 class="ml-3 text-xl sm:text-2xl font-bold text-white">
                <span class="text-cyber-green text-glow">Encrypt</span> File
            </h2>
        </div>

        <p class="text-gray-300 mb-6 text-sm sm:text-base">
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
                color="primary"
            />
        </div>

        <div v-if="processingFile" class="mt-6">
            <div class="flex items-center justify-center space-x-2 text-cyber-green">
                <div class="animate-spin h-5 w-5 text-center">
                    ⟳
                </div>
                <span class="text-glow">Encrypting file...</span>
            </div>
        </div>

        <button
            @click="encryptFile"
            :disabled="!selectedFile || isProcessing"
            class="btn btn-primary w-full mt-6 group"
            :class="{ 'opacity-50 cursor-not-allowed': !selectedFile || isProcessing }"
        >
            <span v-if="isProcessing">Processing...</span>
            <span v-else class="group-hover:text-glow transition-all duration-300">Encrypt File</span>
        </button>

        <div class="fixed bottom-4 right-4 z-50 sm:bottom-6 sm:right-6">
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

        const encryptFile = async () => {
            if (!selectedFile.value || isProcessing.value) return;

            try {
                const formData = new FormData();
                formData.append('file_to_encrypt', selectedFile.value);

                uploadProgress.value = 0;
                processingFile.value = false;

                const response = await axios.post('/encrypt', formData, {
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
                let filenameFromServer = 'encrypted.gpg'; // Fallback
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
                notificationTitle.value = 'File Encrypted Successfully';
                notificationMessage.value = 'Your file has been encrypted and is ready for download.';
                showNotification.value = true;

                uploadProgress.value = 0;
                processingFile.value = false;
            } catch (error) {
                console.error('Encryption error:', error);
                notificationType.value = 'error';
                notificationTitle.value = 'Encryption Failed';
                notificationMessage.value = 'There was an error encrypting your file. Please try again.';
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
            encryptFile
        };
    }
});
</script>
