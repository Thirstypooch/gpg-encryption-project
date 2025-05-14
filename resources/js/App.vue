<template>
  <div class="min-h-screen cyber-background">
    <div class="relative z-10">
      <header class="bg-dark-700/90 backdrop-blur-md border-b border-cyber-blue/30 shadow-neon-cyber-blue">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
              <h1 class="text-xl md:text-2xl font-bold text-white animated-border">
                <span class="text-cyber-blue text-glow">GPG</span>
                <span class="text-white">Encryption Tool</span>
              </h1>
            </div>
          </div>
        </div>
      </header>

      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
          <EncryptionPanel />
          <DecryptionPanel />
        </div>
      </main>

      <footer class="bg-dark-700/90 backdrop-blur-md border-t border-cyber-blue/30 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <p class="text-center text-cyber-blue text-sm terminal-text">
            Secure file encryption and decryption using GPG
          </p>
        </div>
      </footer>
    </div>
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
