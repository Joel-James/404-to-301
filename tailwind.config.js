const colors = require('tailwindcss/colors')

module.exports = {
	purge: ['./app/src/**/*.{vue,js}', './core/views/**/*.php'],
	darkMode: false, // or 'media' or 'class'
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
				teal: colors.lightBlue,
				orange: colors.orange,
			},
		},
	},
	variants: {
		extend: {},
	},
	plugins: [require('@tailwindcss/forms')],
}
