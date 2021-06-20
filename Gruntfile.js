module.exports = function (grunt) {
    require('load-grunt-tasks')(grunt);

    const webpack = require('webpack');

    // Files to include while packing zip.
    var files = [
        'app/**',
        'core/**',
        'uninstall.php',
        'changelog.txt',
        'custom-videos.txt',
        'wpmudev-videos.php',
        '!**/*.map'
    ];

    grunt.initConfig({
        // Package file.
        pkg: grunt.file.readJSON('package.json'),

        // Project constants.
        project: {
            build: './build',
            name: 'wpmudev-videos',
            css: 'app/assets/css',
            scss: 'app/assets/src/scss',
        },

        // Clean build folder.
        clean: {
            main: ['build/'],
            assets: [
                'app/assets/css/**',
                'app/assets/js/**',
                'app/assets/fonts/**',
                'app/assets/img/*.png',
                'app/assets/img/*.svg',
                'app/assets/img/*.jpg',
                'app/assets/img/*.jpeg',
            ],
        },

        // Verify text domains.
        checktextdomain: {
            options: {
                text_domain: 'wpmudev_vids',
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
                    'app/**/*.php',
                    'core/**/*.php',
                    'uninstall.php',
                    'wpmudev-videos.php',
                    '!core/external/**'
                ],
                expand: true
            }
        },

        // Make .pot file.
        makepot: {
            options: {
                domainPath: 'languages',
                exclude: [
                    'core/external/.*'
                ],
                mainFile: 'wpmudev-videos.php',
                potFilename: 'wpmudev-videos.pot',
                potHeaders: {
                    'report-msgid-bugs-to': 'https://wpmudev.org',
                    'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
                },
                type: 'wp-plugin',
                updateTimestamp: false // Update POT-Creation-Date header if no other changes are detected
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
                    cwd: '<%= project.build %>/<%= project.name %>'
                }
            }
        },

        // Copy selected folder and files for release.
        copy: {
            files: {
                src: files,
                dest: '<%= project.build %>/<%= project.name %>/',
                options: {
                    noProcess: ['**/*.{png,gif,jpg,ico,svg,eot,ttf,woff,woff2}'],
                }
            },
        },

        // Compress release folder with version number.
        compress: {
            files: {
                options: {
                    archive: '<%= project.build %>/<%= project.name %>-<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: '<%= project.build %>/<%= project.name %>/',
                src: ['**/*'],
                dest: '<%= project.name %>/'
            },
        },
    });

    grunt.registerTask('prepare', ['checktextdomain']);
    grunt.registerTask('translate', ['makepot:main']);

    grunt.registerTask('build', [
        'clean:main',
        'copy',
        'makepot:release',
        'compress'
    ]);
};
