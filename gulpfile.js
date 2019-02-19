var gulp = require('gulp'),
    gutil = require('gulp-util'),
    sass = require('gulp-sass'),
    browserSync = require('browser-sync').create(),
    filter = require('gulp-filter'),
    plugin = require('gulp-load-plugins')();






// Set local URL if using Browser-Sync
const LOCAL_URL = 'localhost:8888/',

      // Settings for build of assets and jshint
      SOURCE = {
        scripts: [
          // you can add more js file dir here
          'assets/scripts/js/**/*.js'
        ],
        styles: 'assets/styles/sass/**/*.+(scss|sass)',
        images: 'assets/images/**/*',
        php: ['**/*.php', '!inc/**/*.php']
      },

      ASSETS = {
      	styles: 'assets/styles/dist/',
      	scripts: 'assets/scripts/dist/',
      	images: 'assets/images/',
      	all: 'assets/'
      },

      JSHINT_CONFIG = {
      	"node": true,
      	"globals": {
      		"document": true,
      		"jQuery": true
      	}
      };




// GULP FUNCTIONS
// JSHint, concat, and minify JavaScript
gulp.task('scripts', function() {

	// Use a custom filter so we only lint custom JS
	const CUSTOMFILTER = filter(ASSETS.scripts + 'js/**/*.js', {restore: true});

	return gulp.src(SOURCE.scripts)
		.pipe(plugin.plumber(function(error) {
            gutil.log(gutil.colors.red(error.message));
            this.emit('end');
        }))
		.pipe(plugin.sourcemaps.init())
		.pipe(plugin.babel({
			presets: ['env'],
      "plugins": [
        "babel-polyfill",
        'syntax-async-functions'
      ],
			compact: true,
			ignore: ['what-input.js']
		}))
		.pipe(CUSTOMFILTER)
			.pipe(plugin.jshint(JSHINT_CONFIG))
			.pipe(plugin.jshint.reporter('jshint-stylish'))
			.pipe(CUSTOMFILTER.restore)
		.pipe(plugin.concat('scripts.js'))
		.pipe(plugin.uglify())
		.pipe(plugin.sourcemaps.write('.')) // Creates sourcemap for minified JS
		.pipe(gulp.dest(ASSETS.scripts))
});



// Compile Sass, Autoprefix and minify
gulp.task('styles', function() {
	return gulp.src(SOURCE.styles)
		.pipe(plugin.plumber(function(error) {
            gutil.log(gutil.colors.red(error.message));
            this.emit('end');
        }))
		.pipe(plugin.sourcemaps.init())
		.pipe(plugin.sass())
		.pipe(plugin.autoprefixer({
		    browsers: [
		    	'last 2 version', 'safari 5', 'ie 9','ie 10', 'ie 11', 'opera 12.1', 'ios 6', 'android 4'
		    ]
		}))
		.pipe(plugin.cssnano())
		.pipe(plugin.sourcemaps.write('.'))
		.pipe(gulp.dest(ASSETS.styles))
		.pipe(browserSync.reload({
          stream: true
        }));
});

// Optimize images, move into assets directory
// gulp.task('images', function() {
// 	return gulp.src(SOURCE.images)
// 		.pipe(plugin.imagemin())
// 		.pipe(gulp.dest(ASSETS.images))
// });

// watch all php files except
gulp.task('php', function() {
  return gulp.src(SOURCE.php)
});



gulp.task('build', function() {
  console.log('building');
})
// Browser-Sync watch files and inject changes
gulp.task('browsersync', function() {


    browserSync.init({
	    proxy: LOCAL_URL,
    });

    gulp.watch(SOURCE.styles, gulp.parallel('styles'));
    gulp.watch(SOURCE.scripts, gulp.parallel('scripts')).on('change', browserSync.reload);
    gulp.watch(SOURCE.php, gulp.parallel('php')).on('change', browserSync.reload);
    // gulp.watch(SOURCE.images, gulp.parallel('images'));

});
