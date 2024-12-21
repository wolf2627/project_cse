module.exports = function (grunt) {
    // Capture the current date and time to include in file banners
    var currentdate = new Date();
    var datetime = currentdate.getDate() + "/"
        + (currentdate.getMonth() + 1) + "/"
        + currentdate.getFullYear() + " @ "
        + currentdate.getHours() + ":"
        + currentdate.getMinutes() + ":"
        + currentdate.getSeconds();

    // Initialize Grunt configuration
    grunt.initConfig({
        // Concatenation task: combines multiple files into single files for easier management
        concat: {
            options: {
                separator: '\n',                  // Separate concatenated files with a newline
                sourceMap: true,                  // Enable source maps for easier debugging
                banner: '/* Processed on ' + datetime + ' */\n' // Add timestamp banner
            },
            css: {
                src: ['../css/**/*.css'],         // Source: All CSS files in the css directory
                dest: 'dist/style.css',           // Destination: Output single CSS file in dist/
            },
            js: {
                src: ['../js/**/*.js'],           // Source: All JavaScript files in the js directory
                dest: 'dist/app.js'               // Destination: Output single JS file in dist/
            },
            scss: {
                options: { sourceMap: false },   // Disable source maps for SCSS
                src: ['../scss/**/*.scss'],       // Source: All SCSS files in the scss directory
                dest: 'dist/style.scss'           // Destination: Concatenate SCSS files (processed in `sass`)
            }
        },

        // CSS Minification task: reduces CSS file size for better load times
        cssmin: {
            options: {
                mergeIntoShorthands: false,       // Disable shorthand merging to avoid unintended effects
                roundingPrecision: -1             // Keep all decimal places
            },
            css: {
                files: {
                    '../../htdocs/css/style.min.css': ['dist/style.css'], // Minify concatenated CSS
                }
            },
            scss: {
                files: {
                    '../../htdocs/css/app.min.css': ['dist/style.scss'], // Minify processed SCSS output
                }
            }
        },

        // Sass compilation task: compiles SCSS into CSS
        sass: {
            dist: {
                options: {
                    style: 'expanded'            // Set output style to expanded for better readability
                },
                files: {
                    'dist/style.css': 'dist/style.scss' // Compile concatenated SCSS into final CSS
                }
            }
        },

        // JavaScript Minification task: reduces JS file size and enables source maps
        uglify: {
            minify: {
                options: {
                    sourceMap: true              // Generate source maps for easier debugging
                },
                files: {
                    '../../htdocs/js/app.min.js': ['dist/app.js'] // Minify concatenated JS
                }
            }
        },

        // Copy task: can be used to copy files, such as libraries, to a destination directory
        copy: {
            jquery: {
                files: [
                    // Uncomment the following to include jQuery files in the output folder
                    // {
                    //     expand: true,
                    //     flatten: true,
                    //     filter: 'isFile',
                    //     src: ['bower_components/jquery/dist/*'],
                    //     dest: '../../htdocs/js/jquery'
                    // },
                ],
            },
        },

        // Watch task: monitors files for changes and runs associated tasks
        watch: {
            css: {
                files: ['../css/**/*.css'],       // Watch for changes in CSS files
                tasks: ['concat:css', 'cssmin:css'], // Run concatenation and minification on changes
                options: {
                    spawn: false,                 // Use the same process for faster watch execution
                },
            },
            js: {
                files: ['../js/**/*.js'],         // Watch for changes in JavaScript files
                tasks: ['concat:js', 'uglify'],   // Run concatenation and minification on changes
                options: {
                    spawn: false,
                },
            },
            scss: {
                files: ['../scss/**/*.scss'],     // Watch for changes in SCSS files
                tasks: ['concat:scss', 'sass', 'cssmin:scss'], // Concatenate, compile, and minify SCSS files
                options: {
                    spawn: false,
                },
            }
        }
    });

    // Load necessary Grunt plugins for each task
    grunt.loadNpmTasks('grunt-contrib-sass');       // Sass compilation
    grunt.loadNpmTasks('grunt-contrib-copy');       // Copy files
    grunt.loadNpmTasks('grunt-contrib-uglify');     // JavaScript minification
    grunt.loadNpmTasks('grunt-contrib-cssmin');     // CSS minification
    grunt.loadNpmTasks('grunt-contrib-watch');      // File watch
    grunt.loadNpmTasks('grunt-contrib-concat');     // File concatenation

    // Register default task: includes all tasks for development with watch
    grunt.registerTask('default', ['copy', 'concat', 'cssmin', 'sass', 'uglify', 'watch']);

    // Register build task: run all tasks without watch for production
    grunt.registerTask('build', ['copy', 'concat', 'cssmin', 'sass', 'uglify']);
};
