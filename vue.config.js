const path = require('path')
const BrowserSync = require('browser-sync-webpack-plugin')
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries')

// Custom configuration
let configFile = {}
try {
	configFile = require('./.config.json')
} catch (e) {
	configFile = {}
}

// List of source files.
const pages = {
	admin: './app/src/styles/admin.scss',
	logs: './app/src/scripts/modules/logs/main.js',
	'logs-settings': './app/src/scripts/modules/settings/logs.js',
	'general-settings': './app/src/scripts/modules/settings/general.js',
	'redirect-settings': './app/src/scripts/modules/settings/redirect.js',
	'email-settings': './app/src/scripts/modules/settings/email.js',
}

let config = {
	// Disable sourcemap.
	productionSourceMap: false,

	// Disable hashes in filenames.
	filenameHashing: false,

	// Vue JS files to process.
	pages: pages,

	// Assets path.
	publicPath: '/app/assets/',

	// Asset output path.
	outputDir: 'app/assets/',

	css: {
		// Always extract CSS into separate file.
		extract: true,
	},

	// Delete HTML related webpack plugins.
	chainWebpack: (config) => {
		config.plugins.delete('html')
		config.plugins.delete('preload')
		config.plugins.delete('prefetch')
		config.optimization.delete('splitChunks')

		// Remove page for each script.
		Object.keys(pages).forEach((page) => {
			config.plugins.delete(`html-${page}`)
			config.plugins.delete(`preload-${page}`)
			config.plugins.delete(`prefetch-${page}`)
		})

		if (config.plugins.has('extract-css')) {
			let extractCSSPlugin = config.plugin('extract-css')

			extractCSSPlugin &&
				extractCSSPlugin.tap(() => [
					{
						filename: 'css/[name].min.css',
					},
				])
		}

		// Set an alias so we can easily import components.
		config.resolve.alias.set('@', path.join(__dirname, 'app/src/scripts'))
	},

	// Extra webpack configuration.
	configureWebpack: {
		output: {
			// Add .min to js files.
			filename: 'js/[name].min.js',
			chunkFilename: 'js/[name].min.js',
			hotUpdateChunkFilename: 'ot/hot-update.js',
			hotUpdateMainFilename: 'hot/hot-update.json',
		},

		performance: {
			hints: false,
		},

		plugins: [new FixStyleOnlyEntriesPlugin({ silent: true })],

		resolve: {
			alias: {
				'vue$': 'vue/dist/vue.esm.js' // 'vue/dist/vue.common.js' for webpack 1
			}
		}
	},

	devServer: {
		hot: false,
		liveReload: false,
	},
}

// Setup browser sync only if config is set.
if (configFile.settingsUrl) {
	config.configureWebpack.plugins.push(
		new BrowserSync({
			reloadDelay: 0,
			cors: true,
			proxy: {
				target: configFile.settingsUrl,
			},
		})
	)
}

// Setup config.
module.exports = config
