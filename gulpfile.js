'use strict';

var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var browserSync = require('browser-sync');
var del = require('del');
var runSequence = require('run-sequence');
var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');

var browserify = require('browserify');
var watchify = require('watchify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');

var contentInclude = require('gulp-content-includer');

const $ = gulpLoadPlugins();
const reload = browserSync.reload;

const AUTOPREFIXER_BROWSERS = [
    'ie >= 10',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4.4',
    'bb >= 10'
];

gulp.task('clearCache', (done) => {
    return $.cache.clearAll(done);
});

// 图片优化
gulp.task('images', () => {
    return gulp.src('src/images/**/*')
        .pipe($.cache($.imagemin({
            progressive: true,
            interlaced: true
        })))
        .pipe(gulp.dest('public/assets/images'))
        .pipe($.size({
            title: 'images'
        }));
});

// // copy
gulp.task('copy', () => {
    return gulp.src([
        'node_modules/font-awesome/fonts/*',
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/sweetalert/dist/sweetalert.css',
        'node_modules/pace-progress/pace.min.js',
        'node_modules/pace-progress/themes/orange/pace-theme-flash.css',
    ]).
    pipe(gulp.dest((file) => {
            if (file.path.indexOf('jquery') > -1) {
                return 'public/assets/js';
            }
            if (file.path.indexOf('fonts') > -1) {
                return 'public/assets/fonts';
            }
            if (file.path.indexOf('sweetalert') > -1) {
                return 'public/assets/css';
            }
            if (file.path.indexOf('pace-progress') > -1) {
              if (file.path.indexOf('pace.min.js') > -1) {
                  return 'public/assets/js';
              }
              if (file.path.indexOf('themes') > -1) {
                  return 'public/assets/css';
              }
            }
            return 'public/assets';
        }))
        .pipe($.size({
            title: 'copy'
        }));
});

// 处理less
gulp.task('less', () => {
    return gulp.src(['src/less/app.less'])
        // .pipe($.concat('app.less'))
        .pipe($.changed('less', {
            extension: '.less'
        }))
        .pipe($.plumber({
            errorHandler: err => {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe($.less())
        .pipe($.postcss([autoprefixer({
            browsers: AUTOPREFIXER_BROWSERS
        })]))
        .pipe(gulp.dest('public/assets/css'))
        .pipe($.postcss([cssnano()]))
        .pipe($.rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('public/assets/css'))
        .pipe($.size({
            title: 'less'
        }));
});

// 压缩html
gulp.task('html', () => {
    return gulp.src('src/html/**/*.html')
        .pipe(contentInclude({
            includerReg: /<!\-\-include\s+"([^"]+)"\-\->/g
        }))
        // Minify Any HTML
        .pipe($.htmlmin({
            collapseWhitespace: true
        }))
        // Output Files
        .pipe(gulp.dest('application/view'))
        .pipe($.size({
            title: 'html'
        }));
});


// 打包 Common JS 模块
var bundleInit = () => {
  var entryFiles = [
    'src/js/app.js'
  ];

  entryFiles.map(function(index) {
    // var b = watchify(browserify({
    //     entries: index,
    //     basedir: __dirname,
    //     cache: {},
    //     packageCache: {}
    // }));
    var b = browserify({
        entries: index,
        basedir: __dirname,
        cache: {},
        packageCache: {}
    });

    b.transform('babelify', {
            presets: ['es2015']
        })
        // 如果你想把 jQuery 打包进去，注销掉下面一行
        // .transform('browserify-shim', {global: true});

    b.on('update', () => {
        bundle(b);
    }).on('log', $.util.log);

    // bundle(b);
    var sourceFile = index.replace('src/js/','');
    b.bundle()
        .on('error', $.util.log.bind($.util, 'Browserify Error'))
        .pipe(source(sourceFile))
        .pipe(buffer())
        .pipe(gulp.dest('public/assets/js'))
        .pipe($.uglify())
        .pipe($.rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('public/assets/js'));
  });

};

var bundle = (b) => {
    return b.bundle()
        .on('error', $.util.log.bind($.util, 'Browserify Error'))
        .pipe(source('main.js'))
        .pipe(buffer())
        .pipe(gulp.dest('public/assets/js'))
        .pipe($.uglify())
        .pipe($.rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('public/assets/js'));
};

gulp.task('browserify', bundleInit);

// clean
gulp.task('clean', () => {
    return del(['public/assets/*','application/view/*'], {
        dot: true
    });
});

// 清空 gulp-cache 缓存
gulp.task('clearCache', (cb) => {
    return $.cache.clearAll(cb);
});

// 监视源文件变化自动cd编译
gulp.task('watch', () => {
    gulp.watch('src/html/**/*.html', ['html']);
    gulp.watch('src/less/**/*less', ['less']);
    gulp.watch('src/images/**/*', ['images']);
    // 使用 watchify，不再需要使用 gulp 监视 JS 变化
    gulp.watch('src/js/**/*', ['browserify']);
});

// 启动预览服务，并监视 src 目录变化自动刷新浏览器
gulp.task('start', ['default'], () => {
    // browserSync({
    //     notify: false,
    //     // Customize the BrowserSync console logging prefix
    //     logPrefix: 'ASK',
    //     server: 'public'
    // });
    browserSync({
      proxy : "http://orange.cc512.com/",
      notify: false,
      logPrefix: 'ASK',
    });

    gulp.watch(['public/**/*','application/**/*'], reload);
});

// 默认任务
gulp.task('default', (cb) => {
    runSequence('clean', ['images', 'copy', 'less', 'html', 'browserify'], 'watch', cb);
});
