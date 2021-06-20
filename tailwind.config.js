const colors = require('tailwindcss/colors')

module.exports = {
	purge: ['./app/src/**/*.{vue,js}', './app/templates/**/*.php'],
	darkMode: false, // or 'media' or 'class'
	important: true,
	theme: {
		extend: {
			fontFamily: {
				sans: [
					'-apple-system',
					'BlinkMacSystemFont',
					'"Segoe UI"',
					'Roboto',
					'Ubuntu',
					'Cantarell',
					'"Helvetica Neue"',
					'sans-serif',
				],
			},
			colors: {
				wpblue: colors.lightBlue,
				orange: colors.orange,
				'light-blue': colors.lightBlue,
				teal: colors.teal
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [require('@tailwindcss/forms')],
}
