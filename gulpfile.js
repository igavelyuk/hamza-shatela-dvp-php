/**
* Developed by Slidesigma.
* @version 1.0.1
*/

var gulp           = require('gulp');
var runSequence    = require('gulp4-run-sequence');
var replace        = require('gulp-replace');
var rename         = require("gulp-rename");
var injectPartials = require('gulp-inject-partials');
var inject         = require('gulp-inject');
var uglify         = require('gulp-uglify');
var uglifycss      = require('gulp-uglifycss');
var concat         = require('gulp-concat');
var imagemin       = require('gulp-imagemin');
var tap            = require('gulp-tap');
var sass           = require('gulp-sass');
var jimp           = require('jimp');
var del            = require('del');
var purgecss       = require('gulp-purgecss');
var gulpSeo        = require('gulp-seo');
var plumber        = require('gulp-plumber');
var unusedImages   = require('gulp-unused-images');
var browserSync    = require('browser-sync').create();

/* Main Injection function Sequence */
gulp.task('inject', async function() {
  runSequence('injectPartial' , 'replacePath');
});

gulp.task('injectPartial', function () {
  return gulp.src([
    "sections/**/*.html",
  ], { base: "./" })
    .pipe(injectPartials())
    .pipe(gulp.dest("."));
});

/* Redirect internal paths to root */
gulp.task('replacePath', function(){
  return gulp.src(['sections/*/*.html'], { base: "./" })
    .pipe(replace('"assets/', '"../assets/'))
    .pipe(gulp.dest('.'));
});
