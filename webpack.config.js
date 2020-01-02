const _ = require( 'lodash' );
const path = require( 'path' );
const webpack = require( 'webpack' );
const autoprefixer = require( 'autoprefixer' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const { VueLoaderPlugin } = require( 'vue-loader' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );

const sharedConfig = {
	mode: 'production',

	stats: {
		colors: true,
		entrypoints: true,
	},

	watchOptions: {
		ignored: /node_modules/,
		poll: 1000,
	},
};

const scssConfig = _.assign( _.cloneDeep( sharedConfig ), {
	entry: {
		'logs': path.resolve( __dirname, 'app/src/scss/logs.scss' ),
		'settings': path.resolve( __dirname, 'app/src/scss/settings.scss' ),
	},

	output: {
		filename: '[name].min.css',
		path: path.resolve( __dirname, 'app/assets/css' ),
	},

	module: {
		rules: [
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				use: [ MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
					},
					{
						loader: 'postcss-loader',
						options: {
							plugins: [
								autoprefixer(),
							],
							sourceMap: true,
						},
					},
					{
						loader: 'resolve-url-loader',
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: true,
						},
					},
				],
			},
			{
				test: /\.(png|jpg|gif)$/,
				use: {
					loader: 'file-loader',
					options: {
						name: '[name].[ext]',
						outputPath: '../image',
					},
				},
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
				use: {
					loader: 'file-loader',
					options: {
						name: '[name].[ext]',
						outputPath: '../fonts',
					},
				},
			},
		],
	},

	devtool: 'source-map',

	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../css/[name].min.css',
		} ),
		new CleanWebpackPlugin(),
	],
} );

const jsConfig = _.assign( _.cloneDeep( sharedConfig ), {
	entry: {
		'logs': path.resolve( __dirname, 'app/src/js/logs.js' ),
		'settings': path.resolve( __dirname, 'app/src/js/settings.js' ),
	},

	output: {
		filename: '[name].min.js',
		path: path.resolve( __dirname, 'app/assets/js' ),
	},

	resolve: {
		alias: {
			vue: 'vue/dist/vue.js'
		},
		extensions: [ '*', '.js', '.vue', '.json' ],
	},

	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: [ '@babel/preset-env' ]
					}
				}
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			}
		]
	},

	plugins: [
		new VueLoaderPlugin(),
		new CleanWebpackPlugin(),
	],

	optimization: {
		splitChunks: {
			cacheGroups: {
				commons: {
					test: /[\\/]node_modules[\\/]/,
					name( module, chunks, cacheGroupKey ) {
						return 'vendors';
					},
					chunks: 'all'
				}
			}
		}
	}
} );

module.exports = [ jsConfig, scssConfig ];