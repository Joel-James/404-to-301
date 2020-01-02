module.exports = function ( grunt ) {
    require( 'load-grunt-tasks' )( grunt );

    const copyFiles = [
        'app/**',
        '!app/assets/src/**',
        'core/**',
        'languages/**',
        'google-analytics-async.php',
        // We need only the required services.
        'vendor/google/apiclient/**/*',
        'vendor/google/auth/**/*',
        'vendor/google/apiclient-services/src/Google/Service/Analytics.php',
        'vendor/google/apiclient-services/src/Google/Service/Analytics/**/*',
        'vendor/google/apiclient-services/src/Google/Service/AnalyticsReporting.php',
        'vendor/google/apiclient-services/src/Google/Service/AnalyticsReporting/**/*',
        'vendor/google/apiclient-services/src/Google/Service/PeopleService.php',
        'vendor/google/apiclient-services/src/Google/Service/PeopleService/**/*',
        'vendor/google/apiclient-services/src/Google/Service/Oauth2.php',
        'vendor/google/apiclient-services/src/Google/Service/Oauth2/**/*',
        'vendor/firebase/**/*',
        'vendor/guzzlehttp/**/*',
        'vendor/psr/**/*',
        'vendor/monolog/**/*',
        'vendor/ralouphie/**/*',
        'vendor/composer/**/*',
        'vendor/phpseclib/**/*',
        'vendor/autoload.php',
        '!vendor/**/**/{tests,Tests,doc?(s),examples}/**/*',
        '!vendor/**/**/{*.md,*.yml,phpunit.*}',
        '!**/*.map'
    ];

    const excludeCopyFilesPro = copyFiles
        .slice( 0 ).concat( [ 'changelog.txt' ] );

    const excludeCopyFilesFree = copyFiles
        .slice( 0 ).concat( [
            '!core/external/**',
            '!core/class-pro.php',
            'readme.txt',
            '!app/templates/pro/**',
            '!languages/google-analytics-async.pot',
        ] );

    const changelog = grunt.file.read( '.changelog' );

    grunt.initConfig( {
        pkg: grunt.file.readJSON( 'package.json' ),

        // Project variables
        project: {
            css: 'app/assets/css',
            scss: 'app/assets/src/scss',
        },

        // Clean temp folders and release copies.
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
            assets: [
                'app/assets/css/**',
                'app/assets/js/**',
            ],
            folder_v2: ['build/**'],
        },

        checktextdomain: {
            options: {
                text_domain: 'ga_trans',
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
                    '_nx_noop:1,2,3c,4d',
                ],
            },
            files: {
                src: [
                    'app/templates/**/*.php',
                    'core/**/*.php',
                    '!core/external/**', // Exclude external libs.
                    'google-analytics-async.php',
                ],
                expand: true,
            },
        },

        // Generate POT files.
        makepot: {
            options: {
                type: 'wp-plugin',
                domainPath: 'languages',
                potHeaders: {
                    'report-msgid-bugs-to': 'https://wpmudev.org',
                    'language-team': 'LANGUAGE <EMAIL@ADDRESS>',
                },
                exclude: [
                    'node_modules/.*',
                    'core/external/.*',
                    'vendor/.*'
                ],
                updateTimestamp: false,
            },
            main: {
                options: {
                    cwd: '',
                    potFilename: 'google-analytics-async.pot',
                },
            },
            pro: {
                options: {
                    cwd: 'build/google-analytics-async',
                    potFilename: 'google-analytics-async.pot',
                },
            },
            free: {
                options: {
                    cwd: 'build/beehive-analytics',
                    potFilename: 'beehive-analytics.pot',
                },
            },
        },

        copy: {
            pro: {
                src: excludeCopyFilesPro,
                dest: 'build/<%= pkg.name %>/',
            },
            free: {
                src: excludeCopyFilesFree,
                dest: 'build/beehive-analytics/',
            },
        },

        compress: {
            pro: {
                options: {
                    mode: 'zip',
                    archive: './build/<%= pkg.name %>-<%= pkg.version %>.zip',
                },
                expand: true,
                cwd: 'build/<%= pkg.name %>/',
                src: [ '**/*' ],
                dest: '<%= pkg.name %>/',
            },
            free: {
                options: {
                    mode: 'zip',
                    archive: './build/beehive-analytics-<%= pkg.version %>.zip',
                },
                expand: true,
                cwd: 'build/beehive-analytics/',
                src: [ '**/*' ],
                dest: 'beehive-analytics/',
            },
        },

        open: {
            dev: {
                path: '<%= pkg.projectEditUrl %>',
                app: 'Google Chrome',
            },
        },

        search: {
            files: {
                src: [ '<%= pkg.main %>' ],
            },
            options: {
                logFile: 'misc/tmp/log-search.log',
                searchString: /^[ \t\/*#@]*Version:(.*)$/mig,
                onMatch: function ( match ) {
                    const regExp = /^[ \t\/*#@]*Version:(.*)$/mig;
                    const groupedMatches = regExp.exec( match.match );
                    const versionFound = groupedMatches[ 1 ].trim();
                    if ( versionFound !== grunt.file.readJSON( 'package.json' ).version ) {
                        grunt.fail.fatal( 'Plugin version does not match with package.json' +
                            'version. Please, fix.' );
                    }
                },
                onComplete: function ( matches ) {
                    if ( !matches.numMatches ) {
                        if ( !grunt.file.readJSON( 'package.json' ).main ) {
                            grunt.fail.fatal( 'main field is not defined in package.json.' +
                                'Please, add the plugin main file on that field.' );
                        } else {
                            grunt.fail.fatal( 'Version Plugin header not found in ' +
                                grunt.file.readJSON( 'package.json' ).main +
                                ' file or the file does not exist' );
                        }
                    }
                },
            },
        },

        replace: {
            wpid: {
                options: {
                    patterns: [
                        {
                            match: /WDP ID:\s{6}51/g,
                            replacement: '',
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/beehive-analytics/google-analytics-async.php' ],
                        dest: './build/beehive-analytics',
                    },
                ],
            },
            changelogFree: {
                options: {
                    patterns: [
                        {
                            match: /%%CHANGELOG%%/g,
                            replacement: changelog,
                        },
                        {
                            match: /%%VERSION%%/g,
                            replacement: grunt.file.readJSON( 'package.json' ).version,
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/beehive-analytics/readme.txt' ],
                        dest: './build/beehive-analytics',
                    },
                ],
            },
            changelogPro: {
                options: {
                    patterns: [
                        {
                            match: /%%CHANGELOG%%/g,
                            replacement: changelog,
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/google-analytics-async/changelog.txt' ],
                        dest: './build/google-analytics-async',
                    },
                ],
            },
            pluginName: {
                options: {
                    patterns: [
                        {
                            match: /Plugin Name: Beehive Pro/g,
                            replacement: 'Plugin Name: Beehive Analytics',
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/beehive-analytics/google-analytics-async.php' ],
                        dest: './build/beehive-analytics',
                    },
                ],
            },
            pluginUri: {
                options: {
                    patterns: [
                        {
                            match: /http:\/\/premium.wpmudev.org\/project\/google-analytics-for-wordpress-mu-sitewide-and-single-blog-solution\//g,
                            replacement: 'https://wordpress.org/plugins/beehive-analytics/',
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/beehive-analytics/google-analytics-async.php' ],
                        dest: './build/beehive-analytics',
                    },
                ],
            },
            pluginType: {
                options: {
                    patterns: [
                        {
                            match: /define\( 'BEEHIVE_PRO', true \)/g,
                            replacement: 'define( \'BEEHIVE_FREE\', true )',
                        },
                    ],
                },
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: [ './build/beehive-analytics/google-analytics-async.php' ],
                        dest: './build/beehive-analytics',
                    },
                ],
            },
        },

        rename: {
            free: {
                files: [
                    {
                        src: [ 'build/beehive-analytics/google-analytics-async.php' ],
                        dest: 'build/beehive-analytics/beehive-analytics.php'
                    },
                ]
            }
        },

        // TEST - Run the PHPUnit tests.
        phpunit: {
            classes: {
                dir: ''
            },
            options: {
                bin: 'phpunit',
                bootstrap: 'tests/php/bootstrap.php',
                testsuite: 'default',
                configuration: 'tests/php/phpunit.xml',
                colors: true,
                staticBackup: false,
                noGlobalsBackup: false
            }
        },
    } );

    grunt.loadNpmTasks( 'grunt-search' );

    grunt.registerTask( 'version-compare', [ 'search' ] );
    grunt.registerTask( 'test', [ 'phpunit' ] );
    grunt.registerTask( 'finish', function () {
        const json = grunt.file.readJSON( 'package.json' );
        const file = './build/' + json.name + '-' + json.version + '.zip';
        grunt.log.writeln( 'Process finished. Browse now to: ' +
            json.projectEditUrl[ 'green' ].bold );
        grunt.log.writeln( 'And upload the zip file under: ' + file[ 'green' ].bold );
        grunt.log.writeln( 'To make wp.org release, follow the instructions in readme' );
        grunt.log.writeln( '----------' );
        grunt.log.writeln( '' );
        grunt.log.writeln( 'Remember to tag this new version:' );

        const tagMessage = 'git tag -a ' + json.version + ' -m "$CHANGELOG"';
        const pushMessage = 'git push -u origin ' + json.version;
        grunt.log.writeln( tagMessage[ 'green' ] );
        grunt.log.writeln( pushMessage[ 'green' ] );
        grunt.log.writeln( '----------' );
    } );

    grunt.registerTask( 'translate', [
        'checktextdomain',
        'wpvuei18n makepot:main',
    ] );

    grunt.registerTask( 'build', [
        'checktextdomain',
        'copy:pro',
        'makepot:pro',
        'makepot:pro',
        'replace:changelogPro',
        'compress:pro',
    ] );

    grunt.registerTask( 'build:free', [
        'checktextdomain',
        'copy:free',
        'makepot:free',
        'replace:wpid',
        'replace:changelogFree',
        'replace:pluginName',
        'replace:pluginUri',
        'replace:pluginType',
        'rename:free',
        'compress:free',
    ] );
};
