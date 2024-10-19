// eslint.config.js
import eslintPluginPrettier from 'eslint-plugin-prettier';
import eslintPluginTypeScript from '@typescript-eslint/eslint-plugin';

export default [
  {
    files: ['**/*.ts', '**/*.tsx'], // Specify TypeScript files
    languageOptions: {
      ecmaVersion: 2020, // ECMAScript version
      sourceType: 'module' // ES Modules
    },
    plugins: {
      '@typescript-eslint': eslintPluginTypeScript, // Use default import for CommonJS module
      prettier: eslintPluginPrettier // Use default import for Prettier
    },
    rules: {
      // TypeScript-specific rules
      '@typescript-eslint/no-unused-vars': 'error',
      '@typescript-eslint/explicit-function-return-type': 'warn',

      // Prettier rules
      'prettier/prettier': 'error' // Enforces Prettier formatting as ESLint errors
    }
  }
];
