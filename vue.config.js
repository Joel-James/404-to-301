const path = require( 'path' );

// List of source files.
const pages = {
	'logs': './app/src/modules/logs/main.js',
	'settings': './app/src/modules/settings/main.js',
};

module.exports = {
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
		extract: true
	},

	// Delete HTML related webpack plugins.
	chainWebpack: config => {
		config.plugins.delete( 'html' );
		config.plugins.delete( 'preload' );
		config.plugins.delete( 'prefetch' );

		// Remove page for each script.
		Object.keys( pages ).forEach( page => {
			config.plugins.delete( `html-${ page }` );
			config.plugins.delete( `preload-${ page }` );
			config.plugins.delete( `prefetch-${ page }` );
		} );

		if ( config.plugins.has( 'extract-css' ) ) {
			let extractCSSPlugin = config.plugin( 'extract-css' );

			extractCSSPlugin &&
			extractCSSPlugin.tap( () => [ {
				filename: 'css/[name].min.css'
			} ] );
		}

		// Set an alias so we can easily import components.
		config.resolve.alias.set( '@', path.join( __dirname, 'app/src' ) );
	},

	// Extra webpack configuration.
	configureWebpack: {
		output: {
			// Add .min to js files.
			filename: 'js/[name].min.js',
			chunkFilename: 'js/[name].min.js'
		},

		performance: {
			hints: false
		},

		optimization: {
			splitChunks: {
				minSize: 1
			}
		}
	},
};