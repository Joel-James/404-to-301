module.exports = function ( grunt ) {

	require( 'load-grunt-tasks' )( grunt );

	var conf = {
		plugin_branches: {
			include_files: [
				'core/**',
				'app/**',
				'404-to-301.php',
				'uninstall.php',
				'LICENSE.txt'
			]
		},

		plugin_dir: '404-to-301/',
		plugin_file: '404-to-301.php'
	};

	// Project configuration.
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		// Make .pot file for translation.
		makepot: {
			options: {
				domainPath: 'languages',
				exclude: [
					'inc/vendor/.*'
				],
				mainFile: '404-to-301.php',
				potFilename: '404-to-301.pot',
				potHeaders: {
					'poedit': true,
					'language-team': 'Duck Dev <contact@duckdev.org>',
					'report-msgid-bugs-to': 'https://duckdev.com/',
					'last-translator': 'Joel James <contact@duckdev.org>',
					'x-generator': 'grunt-wp-i18n'
				},
				type: 'wp-plugin',
				updateTimestamp: false, // Update POT-Creation-Date header if no other changes are detected.
				cwd: ''
			},
			// Make .pot file for the plugin.
			main: {
				options: {
					cwd: ''
				}
			},
			// Make .pot file for the release.
			release: {
				options: {
					cwd: 'releases/404-to-301'
				}
			}
		},

		// Make .mo file from .pot file for translation.
		po2mo: {
			// Make .mo file for the plugin.
			main: {
				src: 'languages/404-to-301.pot',
				dest: 'languages/404-to-301.mo'
			},
			// Make .mo file for the release.
			release: {
				src: 'releases/404-to-301/languages/404-to-301.pot',
				dest: 'releases/404-to-301/languages/404-to-301.mo'
			}
		},

		// Clean temp folders.
		clean: {
			temp: {
				src: [
					'**/*.tmp',
					'**/.afpDeleted*',
					'**/.DS_Store',
				],
				dot: true,
				filter: 'isFile'
			},
			folder_v2: [
				'releases/**',
				'assets/css/**',
				'assets/js/**',
				'assets/fonts/**'
			]
		},

		// Verify in text domain is used properly.
		checktextdomain: {
			options: {
				text_domain: '404-to-301',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: [
					'inc/admin/*.php',
					'inc/core/*.php',
					'inc/front/*.php',
					'inc/helpers/*.php',
					'inc/utils/*.php',
					'404-to-301.php',
					'uninstall.php'
				],
				expand: true
			}
		},

		// Copy selected folder and files for release.
		copy: {
			files: {
				src: conf.plugin_branches.include_files,
				dest: 'releases/<%= pkg.name %>/'
			}
		},

		// Compress release folder with version number.
		compress: {
			files: {
				options: {
					mode: 'zip',
					archive: './releases/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'releases/<%= pkg.name %>/',
				src: ['**/*'],
				dest: conf.plugin_dir
			}
		}
	} );

	// Check if text domain is used properly.
	grunt.registerTask( 'prepare', ['checktextdomain'] );

	// Make pot file from files.
	grunt.registerTask( 'translate', ['makepot:main', 'po2mo:main'] );

	// Run build task to create release copy.
	grunt.registerTask( 'build', 'Run all tasks.', function () {
		grunt.task.run( 'clean' );
		grunt.task.run( 'translate' );
		grunt.task.run( 'copy' );
		grunt.task.run( 'makepot:release' );
		grunt.task.run( 'po2mo:release' );
		grunt.task.run( 'compress' );
	} );
};