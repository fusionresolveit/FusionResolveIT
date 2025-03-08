import { defineConfig } from 'cypress';

export default defineConfig({
  e2e: {
    experimentalStudio: true,
    baseUrl: 'http://localhost',
    video: true,
  },
  viewportWidth: 1280,
});
