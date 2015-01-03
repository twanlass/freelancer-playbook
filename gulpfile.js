////////////////////////////////////////
// Load Plugins
////////////////////////////////////////

var DEST = 'public/_themes/base',
    gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'),
    cache = require('gulp-cache'),
    changed = require('gulp-changed'),
    clean = require('gulp-clean'),
    coffee = require('gulp-coffee'),
    concat = require('gulp-concat'),
    connect = require('gulp-connect'),
    gutil = require('gulp-util');
    haml = require('gulp-haml'),
    imagemin = require('gulp-imagemin'),
    jshint = require('gulp-jshint'),
    markdown = require('gulp-markdown'),
    minifyCSS = require('gulp-minify-css'),
    notify = require("gulp-notify"),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass'),
    using = require('gulp-using'),
    uglify = require('gulp-uglify'),
    gulpif = require('gulp-if'),
    argv = require('yargs').argv,
    onError = function(error) {
      gutil.beep();
      gutil.log(gutil.colors.red(error));
      console.log(error);
    }


function onPlumberError() {
  gutil.log(gutil.colors.red(error));
}

////////////////////////////////////////
// Process Assets
////////////////////////////////////////

// Haml
gulp.task('hamls', function() {
  return gulp.src('_build/**/*.haml')
    .pipe(changed(DEST, {extension: '.html'}))
    
    .pipe(plumber({errorHandler: notify.onError({title: 'ERROR', message: "<%= error.message %>"})}))
    .pipe(haml({ compiler: 'visionmedia' }))
    .pipe(gulp.dest(DEST))
    .pipe(connect.reload())
    .pipe(notify({title: 'Note:', message: "Haml <%= file.relative %> compiled"}));
});


// Styles
gulp.task('styles', function() {
  return gulp.src('_build/css/main.scss')
    .pipe(plumber(onError))
    .pipe(sass({
        errLogToConsole: true,
        sourceComments: 'map',
        sourceMap: true,
        style: 'compact'
    }))
    .pipe(autoprefixer({ 
      browsers: ['> 5%', 'safari > 5', 'ie > 8', 'opera 12.1', 'ios > 6', 'android > 4']
    }))
    .pipe(gulpif(argv.production, minifyCSS()))
    .pipe(gulp.dest(DEST + '/css'))
    .pipe(rename(DEST + '/css/base.css'))
    .pipe(connect.reload())
    .pipe(notify({title: 'Note:', message: "Sass <%= file.relative %> compiled"}));
});


// Coffee
gulp.task('coffeescripts', function() {
  return gulp.src(['_build/js/**/*.coffee',
                   '_build/js/*.coffee'],
                  { base: '_build/'})
    .pipe(plumber(onError))
    .pipe(concat('base.coffee'))
    .pipe(coffee())
    .pipe(gulpif(argv.production, uglify()))
    .pipe(gulp.dest(DEST + '/js'))
    .pipe(rename(DEST + '/js/base.js'))
    .pipe(connect.reload())
    .pipe(notify({title: 'Note:', message: "Coffee <%= file.relative %> compiled"}));
});


// JS
gulp.task('javascripts', function() {
  return gulp.src('_build/js/**/*.js')
    .pipe(plumber(onError))
    .pipe(concat('vendor.js'))
    .pipe(gulpif(argv.production, uglify()))
    .pipe(gulp.dest(DEST + '/js'))
    .pipe(rename(DEST + '/js/vendor.js'))
    .pipe(connect.reload());
});


////////////////////////////////////////
// Copy Static Files (img, fonts)
////////////////////////////////////////

// Copy images
gulp.task('copy-images', function() {
  gulp.src('_build/img/**/*.*', { cwd: './' })
  .pipe(gulp.dest(DEST + '/img'));
});


// Copy fonts
gulp.task('copy-fonts', function() {
  gulp.src('_build/fonts/**/*.*', { cwd: './' })
  .pipe(gulp.dest(DEST + '/fonts'));
});


// Copy config files
gulp.task('copy-config', function() {
  gulp.src('_build/*.yaml', { cwd: './' })
  .pipe(gulp.dest(DEST + '/'));
});


// Copy html files
gulp.task('copy-html', function() {
  gulp.src('_build/*/**.html', { cwd: './' })
  .pipe(gulp.dest(DEST + '/'));
});

////////////////////////////////////////
// Clean Output Directories
////////////////////////////////////////

// Clean
gulp.task('clean', function() {
  return gulp.src(['public/_themes/base/*', '!' + DEST + '/theme.yaml'], {read: false})
    .pipe(clean());
});


// Clean static files
gulp.task('clean-static', function() {
  return gulp.src(['public/_themes/base/img', DEST + '/fonts'], {read: false})
    .pipe(clean());
});



////////////////////////////////////////
// Watch files & rebuild when needed
////////////////////////////////////////
//
gulp.task('watch', function() {

  // Watch .haml files
  gulp.watch('_build/**/*.haml', ['hamls']);

  // Watch .scss files
  gulp.watch('_build/css/**/*.scss', ['styles']);

  // Watch js/coffee files
  gulp.watch(['_build/js/**/*.js', '_build/js/*.coffee', '_build/js/**/*.coffee',], ['coffeescripts', 'javascripts']);

  // Watch image files
  gulp.watch(['_build/img/**/*.*'], ['copy-images']);

  // Watch font files
  gulp.watch(['_build/fonts/**/*.*'], ['copy-fonts']);

  // Watch config files
  gulp.watch(['_build/*.yaml'], ['copy-config']);

  // Watch HTML files
  gulp.watch(['_build/*/**.html'], ['copy-html']);

});
//

////////////////////////////////////////
// Entry Points - default build tasks
////////////////////////////////////////

// Default development task (live reload, etc)
gulp.task('default', ['connect','clean', 'watch'], function() {
    gulp.start('styles', 'coffeescripts', 'javascripts', 'hamls', 'copy-images', 'copy-fonts', 'copy-config', 'copy-html');
});


// Build development task - $ gulp build
gulp.task('build', ['clean'], function() {
    gulp.start('styles', 'coffeescripts', 'javascripts', 'hamls', 'copy-images', 'copy-fonts', 'copy-config', 'copy-html');
});


gulp.task('connect', function() {
  connect.server({
    root: 'public',
    livereload: true
  });
});
