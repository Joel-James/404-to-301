module.exports = {
	root: true,
	env: {
		node: true,
		browser: true,
		es6: true,
	},
	extends: ['eslint:recommended', 'plugin:react/recommended'],
	parserOptions: {
		parser: 'babel-eslint',
		ecmaVersion: 'latest',
		sourceType: 'module',
	},
	rules: {
		'react/prop-types': 'off',
		'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
		'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
	},
}
