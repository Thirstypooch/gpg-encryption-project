<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 to-primary-50">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center">
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
