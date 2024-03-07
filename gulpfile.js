"use strict";

const gulp = require("gulp");
const plumber = require("gulp-plumber");
const rename = require("gulp-rename");
const uglify = require('gulp-uglify');
const notify = require('gulp-notify');
const babel = require('gulp-babel');

const sites = ['backend', 'frontend', 'ariah', 'yopago'];
const dir_name = 'cart';
const dir_path = '../../../';
const frontend_path = `${dir_path}${sites[1]}/web/js/${dir_name}`;
const backend_path = `${dir_path}${sites[0]}/web/js/${dir_name}`;
const ariah_path = `${dir_path}${sites[2]}/web/js/${dir_name}`;
const yopago_path = `${dir_path}${sites[3]}/web/js/${dir_name}`;

function commonUglify() {
  return gulp
    .src('./source/javascript/**/*.js')
    .pipe(babel())
    .pipe(plumber({errorHandler: errorScripts}))
    .pipe(gulp.dest(frontend_path))
    .pipe(gulp.dest(backend_path))
    .pipe(gulp.dest(ariah_path))
    .pipe(gulp.dest(yopago_path))
    .pipe(uglify())
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(gulp.dest(frontend_path))
    .pipe(gulp.dest(backend_path))
    .pipe(gulp.dest(ariah_path))
    .pipe(gulp.dest(yopago_path))

}

function watchJs() {
  gulp.watch(["./source/javascript/**/*"], commonUglify);
}

function errorScripts(error) {
  notify.onError({
    title: "JS Error",
    message: "", sound: "Sosumi"
  })(error);
  console.log(error.toString());
  this.emit("end");
};

// Define complex tasks
// const vendor = gulp.series(clean, modules);
const build = gulp.series(commonUglify);
const watch = gulp.series(build, gulp.parallel(watchJs));

// Export tasks
exports.commonUglify = commonUglify;
exports.build = build;
exports.watch = watch;
exports.default = build;