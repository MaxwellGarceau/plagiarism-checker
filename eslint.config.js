// eslint.config.js
import eslintPluginPrettier from 'eslint-plugin-prettier';
import eslintPluginTypeScript from '@typescript-eslint/eslint-plugin';
import typescriptParser from '@typescript-eslint/parser';

export default [
  {
    files: ['**/*.ts', '**/*.tsx'], // Specify TypeScript files
    languageOptions: {
      ecmaVersion: 2020, // ECMAScript version
      sourceType: 'module', // ES Modules
      parser: typescriptParser // Use TypeScript parser
    },
    plugins: {
      '@typescript-eslint': eslintPluginTypeScript,
      prettier: eslintPluginPrettier
    },
    rules: {
      // TypeScript-specific rules
      '@typescript-eslint/no-unused-vars': 'error',
      '@typescript-eslint/explicit-function-return-type': 'warn',

      // Indentation rule - enforces tabs
      'indent': ['error', 'tab'],

      // Prettier rules
      'prettier/prettier': 'error' // Enforces Prettier formatting as ESLint errors
    }
  }
];
