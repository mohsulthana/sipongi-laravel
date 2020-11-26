module.exports = {
    root: true,
    env: {
        browser: true,
    },
    parserOptions: {
        parser: 'babel-eslint',
    },
    extends: [
        'prettier',
        'prettier/vue',
        'plugin:prettier/recommended',
        'plugin:vue/recommended',
    ],
    plugins: ['prettier'],
    // add your custom rules here
    rules: {
        'no-console': 0,
        'vue/no-v-html': 0,
    },
}
