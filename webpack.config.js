const _ = require( 'lodash' ),
	path = require( 'path' ),
	webpack = require( 'webpack' ),
	ATP = require( 'autoprefixer' ),
	CSSExtract = require( "mini-css-extract-plugin" );

// The path where the Shared UI fonts & images should be sent.
const config = {
	output: {
		imagesDirectory: '../images',
		fontsDirectory: '../fonts'
	}
};

const sharedConfig = {
	mode: 'production',

	stats: {
		colors: true,
		entrypoints: true
	},

	watchOptions: {
		ignored: /node_modules/,
		poll: 1000
	}
};

const scssConfig = _.assign( _.cloneDeep( sharedConfig ), {
	entry: {
		'admin': './assets/src/scss/admin/admin.scss',
		'public': './assets/src/scss/public/frontend.scss'
	},

	output: {
		filename: '[name].min.css',
		path: path.resolve( __dirname, 'assets/css' )
	},

	module: {
		rules: [
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				use: [CSSExtract.loader,
					{
						loader: 'css-loader'
					},
					{
						loader: 'postcss-loader',
						options: {
							plugins: [
								ATP( {
									browsers: ['ie > 9', '> 1%']
								} )
							],
							sourceMap: true
						}
					},
					{
						loader: 'resolve-url-loader'
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: true
						}
					}
				]
			},
			{
				test: /\.(png|jpg|gif)$/,
				use: {
					loader: 'file-loader', // Instructs webpack to emit the required object as file and to return its public URL.
					options: {
						name: '[name].[ext]',
						outputPath: config.output.imagesDirectory
					}
				}
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
				use: {
					loader: 'file-loader', // Instructs webpack to emit the required object as file and to return its public URL.
					options: {
						name: '[name].[ext]',
						outputPath: config.output.fontsDirectory
					}
				}
			}
		]
	},

	plugins: [
		new CSSExtract( {
			filename: '../css/[name].min.css'
		} )
	]
} );

const jsConfig = _.assign( _.cloneDeep( sharedConfig ), {
	entry: {
		'admin/admin': './assets/src/js/admin/admin.js',
		'public/frontend': './assets/src/js/public/frontend.js'
	},

	output: {
		filename: '[name].min.js',
		path: path.resolve( __dirname, 'assets/js' )
	},

	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['env', 'react']
					}
				}
			}
		]
	}
} );

module.exports = [scssConfig, jsConfig];
