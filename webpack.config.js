const path = require('path'),
	ATP = require('autoprefixer'),
	CSSExtract = require('mini-css-extract-plugin')

const TerserPlugin = require('terser-webpack-plugin') // Included with Webpack v5.
const { CleanWebpackPlugin } = require('clean-webpack-plugin')

// The path where the Shared UI fonts & images should be sent.
const config = {
	output: {
		imagesDirectory: '../images',
		fontsDirectory: '../fonts',
	},
}

module.exports = {
	mode: 'production',

	entry: {
		logs: './app/src/logs.js',
		settings: './app/src/settings.js',
		redirects: './app/src/redirects.js',
	},

	output: {
		path: path.resolve(__dirname, 'app/assets/js'),
		filename: '[name].min.js',
		publicPath: '', // Path will be appended to all assets in CSS files.
	},

	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				include: [path.resolve(__dirname, 'app/src')],
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/env', '@babel/react'],
					},
				},
			},
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				use: [
					CSSExtract.loader,
					{
						loader: 'css-loader',
					},
					{
						loader: 'postcss-loader',
						options: {
							postcssOptions: {
								plugins: [ATP()],
							},
							sourceMap: true,
						},
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
					loader: 'file-loader', // Instructs webpack to emit the required object as file and to return its public URL.
					options: {
						name: '[name].[ext]',
						outputPath: config.output.imagesDirectory,
					},
				},
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
				use: {
					loader: 'file-loader', // Instructs webpack to emit the required object as file and to return its public URL.
					options: {
						name: '[name].[ext]',
						outputPath: config.output.fontsDirectory,
					},
				},
			},
		],
	},

	resolve: {
		extensions: ['.js', '.jsx'],
		alias: {
			'@': path.join(__dirname, 'app/src'),
			react: path.resolve('./node_modules/react'),
		},
	},

	devtool: 'source-map',

	stats: {
		colors: true,
		entrypoints: true,
	},

	watchOptions: {
		ignored: /node_modules/,
		poll: 1000,
	},

	plugins: [
		new CSSExtract({
			filename: '../css/[name].min.css',
		}),
		new CleanWebpackPlugin(),
	],

	externals: {
		jquery: 'jQuery',
	},

	optimization: {
		minimize: true,
		minimizer: [
			new TerserPlugin({
				terserOptions: {
					format: {
						comments: false,
					},
				},
				extractComments: false,
			}),
		],
	},
}
