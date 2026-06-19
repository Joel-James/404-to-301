/**
 * Prettier configuration.
 *
 * Based on the WordPress coding standards (`@wordpress/prettier-config`,
 * which the `@wordpress/scripts` ESLint setup merges this file over),
 * with two deliberate, project-wide deviations:
 *
 *   - `semi: false`         — this codebase omits statement semicolons.
 *   - `parenSpacing: false` — no spaces inside parentheses, i.e.
 *                             `foo(bar)` rather than wp-prettier's
 *                             default `foo( bar )`.
 *
 * Everything else mirrors the WordPress defaults so the output stays
 * consistent with core's JavaScript style. Run `npm run format` to
 * apply it and `npm run lint:js` to verify.
 */
module.exports = {
	useTabs: true,
	tabWidth: 4,
	printWidth: 80,
	singleQuote: true,
	semi: false,
	parenSpacing: false,
	trailingComma: 'all',
	arrowParens: 'always',
	bracketSpacing: true,
	bracketSameLine: false,
	overrides: [
		{
			// CSS/SCSS use double quotes per the WordPress CSS standard.
			files: '*.{css,sass,scss}',
			options: {
				singleQuote: false,
			},
		},
	],
}
