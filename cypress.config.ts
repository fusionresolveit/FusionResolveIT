import { defineConfig } from 'cypress';

export default defineConfig({
  e2e: {
    experimentalStudio: true,
    baseUrl: 'http://localhost',
  },
  viewportWidth: 1280,
});
