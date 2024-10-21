/**
 * https://vitest.dev/config/
 */

import { configDefaults, defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config'

export default mergeConfig(viteConfig, defineConfig({
  test: {
    environment: 'jsdom',
    include: ['./src/assets/tests/**.{js,ts}'],
    exclude: [
      ...configDefaults.exclude, 
      '**/node_modules/**',
      '**/dist/**',
      '**/wp/**',
    ],
    setupFiles: ["./vitest-setup.ts"],
    coverage: {
      include: ['src/assets/tests/**'],
      all: true,
    }
  },
}))