var gulp = require('gulp');
var pref = require('gulp-autoprefixer');
var gcat = require('gulp-concat');
var mcss = require('gulp-clean-css');
var name = require('gulp-rename');
var sass = require('gulp-sass');
var ugli = require('gulp-uglify');
var strp = require('gulp-strip-comments');
var comp = require('gulp-remove-empty-lines');
var pump = require('pump');

gulp.task('apply-theme-css',function(){
    /* Combine & minify theme css. */
    pump([
        gulp.src([
            'dev/css/theme/admin.css',
            'dev/css/theme/admin-skin.css'
        ]),
        mcss(),gcat('theme.min.css'),
        gulp.dest('assets/css/')
    ]);
});
gulp.task('apply-theme-js',function(){
    gulp.src('dev/cache/theme.js')
    .pipe(ugli())
    .pipe(gulp.dest('assets/js/'))
});

gulp.task('apply-scss',function(){
    /* Compile & minify css. */
    //pump([
        gulp.src([
            'dev/scss/index-layout.scss','dev/scss/results-layout.scss',
            'dev/scss/frontend-common.scss','dev/scss/backend.min.scss'
        ])
        .pipe(sass())
        .pipe(pref())
        .pipe(mcss())
        .pipe(gulp.dest('assets/css/'));
    //]);
});
gulp.task('frontend-js',function(){
    pump([
        gulp.src([
            'dev/js/frontend_app.utilities.js',
            'dev/js/frontend_app.favorites.js',
            'dev/js/frontend_app.results.js',
            'dev/js/frontend_app.media_box.js',
            'dev/js/frontend_app.modal_media.js',
            'dev/js/frontend_app.photo_page_box.js',
            'dev/js/frontend_app.video_page_box.js'
        ]),
        gcat('frontend-app.js'),
        strp(),comp(),
        gulp.dest('assets/js/')
    ]);
});
gulp.task('backend-js',function(){
    //pump([
        gulp.src([
            'dev/js/backend.js',
            'dev/js/modals.js',
            'dev/js/video_modal.js',
            'dev/js/admin_page.content.js',
            'dev/js/admin_page.sidebar.js',
            'dev/js/admin_app.user.js',
            'dev/js/admin_app.user_editor.js',
            'dev/js/admin_app.role.js',
            'dev/js/admin_app.role_editor.js',
            'dev/js/admin_app.library.js',
            'dev/js/admin_app.photo_editor.js',
            'dev/js/admin_app.video_editor.js',
            'dev/js/admin_app.category.js',
            'dev/js/admin_app.category_editor.js',
            'dev/js/admin_app.category_selector.js',
            'dev/js/admin_app.file_widget.js',
            'dev/js/admin_app.uploader.js'
        ])
        .pipe(gcat('backend.js'))
        .pipe(strp())
        .pipe(comp({removeSpaces:true}))
        .pipe(gulp.dest('assets/js/'))
    //]);
});

gulp.task('watch',function(){
    gulp.watch('dev/js/*',['frontend-js','backend-js']);
    gulp.watch('dev/scss/*.scss',['apply-scss']);
});

// Default.
gulp.task('default',[]);
