const gulp = require('gulp');
const rename = require('gulp-rename');
const requireDir = require('require-dir');

// load ENV
// find values in process.env.${value}, e. g. process.env.APP_ENV
require('dotenv').load();

// Require all tasks in gulpfile.js/tasks, including subfolders
requireDir('./tasks', { recurse: true });

// define sequences
const tasks = {};
tasks.development = gulp.series(
    'copyModule',
    'scripts',
    'copyScript',
    'watch'
);
tasks.production = gulp.series(
    // run tasks in parallel because production mode can be much slower
    gulp.parallel(
        'copyModule',
        'scripts'
    )
);

// define tasks
gulp.task('development', tasks.development);
gulp.task('production', tasks.production);

// define default alias (use from ENV)
gulp.task('default', tasks[process.env.APP_ENV]);