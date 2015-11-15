module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		sass: {
			options: {
				style: 'compressed',
				sourcemap: 'none',
				cacheLocation: 'styles/sass/.sass-cache'
			},
			styles: {
				files: {
					'styles/css/admin.css': 'styles/sass/admin.scss'
				}
			}
		},

		postcss: {
			options: {
				map: false,
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
				sourceMap: false
			},
			scripts: {
				files: {
					'scripts/facebook.min.js': 'scripts/facebook.js'
				}
			}
		},

		watch: {
			scripts: {
				files: [
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

	grunt.registerTask('compile', [
		'sass',
		'postcss',
		'uglify'
	]);

};
