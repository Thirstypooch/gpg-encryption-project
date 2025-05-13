<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 to-primary-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center">
            <svg class="h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <h1 class="ml-2 text-xl font-semibold text-secondary-900">GPG Encryption Tool</h1>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <EncryptionPanel />
        <DecryptionPanel />
      </div>
    </main>

    <footer class="bg-white mt-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <p class="text-center text-secondary-500 text-sm">
          Secure file encryption and decryption using GPG
        </p>
      </div>
    </footer>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import EncryptionPanel from './components/EncryptionPanel.vue';
import DecryptionPanel from './components/DecryptionPanel.vue';

export default defineComponent({
  name: 'App',
  components: {
    EncryptionPanel,
    DecryptionPanel
  }
});
</script>
