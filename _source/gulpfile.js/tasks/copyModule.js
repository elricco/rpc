const gulp = require('gulp');
const rename = require('gulp-rename');
const log = require('fancy-log');
const colors = require('ansi-colors');

// load config
const config = require('../config');

let tasks = [];

// loop through copy tasks (see config) and fill an array with results from copy functions (promises!)
for (let v of config.copyModule) {
    const copyTask = () => Promise.all([
        new Promise(function(resolve, reject) {
            gulp.src(v.sourceFile)
                .pipe(rename({basename: v.basename}))
                .pipe(gulp.dest(v.destinationFolder))
                .on('end', resolve)
        })
            ]).then((res) => {
                    if (res.length > 0) {
                        log(colors.white('Copied ' + v.title + ': ' + colors.magenta(res.length)));
                    }
                });

    copyTask.displayName = 'copy:' + v.title;
    tasks.push(copyTask);
}

const task = gulp.series(tasks);

gulp.task('copyModule', task);
module.exports = task;