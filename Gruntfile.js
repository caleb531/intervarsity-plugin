// Config file for Grunt, which enables automatic style/script compilation
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				cacheLocation: 'styles/sass/.sass-cache'
			},
			external: {
				options: {
					sourcemap: 'file'
				},
				files: {
					'styles/css/admin.css': 'styles/sass/admin.scss',
					'styles/css/datepicker.css': 'styles/sass/datepicker.scss'
				}
			}
		},

		postcss: {
			options: {
				map: true,
				processors: [
					require('autoprefixer')({
						browsers: 'last 2 versions'
					})
				]
			},
			styles: {
				src: 'styles/css/*.css'
			}
		},

		uglify: {
			options: {
				sourceMap: true
			},
			scripts: {
				files: {
					'scripts/datepicker.min.js': 'scripts/datepicker.js',
					'scripts/facebook.min.js': 'scripts/facebook.js'
				}
			}
		},

		watch: {
			scripts: {
				files: [
					'scripts/datepicker.js',
					'scripts/facebook.js'
				],
				tasks: [
					'uglify'
				]
			},
			styles: {
				files: [
					'styles/sass/*.scss'
				],
				tasks: [
					'sass',
					'postcss'
				]
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-postcss');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('build', [
		'sass',
		'postcss',
		'uglify'
	]);

	grunt.registerTask('serve', [
		'build',
		'watch'
	]);

};
