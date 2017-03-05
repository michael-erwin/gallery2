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

var scss_files = [
            './dev/scss/index-layout.scss',
            './dev/scss/results-layout.scss',
            './dev/scss/frontend-common.scss',
            './dev/scss/portal.min.scss',
            './dev/scss/backend.min.scss'
        ];

var frontend_js_files = [
            './dev/js/frontend/frontend_app.utilities.js',
            './dev/js/frontend/frontend_app.favorites.js',
            './dev/js/frontend/frontend_app.results.js',
            './dev/js/frontend/frontend_app.media_box.js',
            './dev/js/frontend/frontend_app.modal_media.js',
            './dev/js/frontend/frontend_app.photo_page_box.js',
            './dev/js/frontend/frontend_app.video_page_box.js'
        ];
var portal_js_files = [
            './dev/js/portal/portal_app.signin.js',
            './dev/js/portal/portal_app.signup.js',
            './dev/js/portal/portal_app.forgot.js',
            './dev/js/portal/portal_app.reset.js'
        ];
var backend_js_files = [
            './dev/js/backend/backend.js',
            './dev/js/backend/modals.js',
            './dev/js/video_modal.js',
            './dev/js/backend/admin_page.content.js',
            './dev/js/backend/admin_page.sidebar.js',
            './dev/js/backend/admin_app.user.js',
            './dev/js/backend/admin_app.user_editor.js',
            './dev/js/backend/admin_app.role.js',
            './dev/js/backend/admin_app.role_editor.js',
            './dev/js/backend/admin_app.library.js',
            './dev/js/backend/admin_app.photo_editor.js',
            './dev/js/backend/admin_app.video_editor.js',
            './dev/js/backend/admin_app.category.js',
            './dev/js/backend/admin_app.category_editor.js',
            './dev/js/backend/admin_app.category_selector.js',
            './dev/js/backend/admin_app.file_widget.js',
            './dev/js/backend/admin_app.uploader.js'
        ];

gulp.task('apply-theme-css',function(){
    /* Combine & minify theme css. */
    pump([
        gulp.src([
            './dev/css/theme/admin.css',
            './dev/css/theme/admin-skin.css'
        ]),
        mcss(),gcat('theme.min.css'),
        gulp.dest('./assets/css/')
    ]);
});

gulp.task('apply-theme-js',function(){
    gulp.src('./dev/cache/theme.js')
    .pipe(ugli())
    .pipe(gulp.dest('./assets/js/'))
});

gulp.task('apply-scss',function(){
    /* Compile & minify css. */
    //pump([
        gulp.src(scss_files)
        .pipe(sass())
        .pipe(pref())
        .pipe(mcss())
        .pipe(gulp.dest('./assets/css/'));
    //]);
});

gulp.task('compile-frontend-js',function(){
    //pump([
        gulp.src(frontend_js_files)
        .pipe(gcat('frontend.js'))
        .pipe(strp())
        .pipe(comp({removeSpaces:true}))
        .pipe(gulp.dest('./assets/js/'))
    //]);
});

gulp.task('compile-portal-js',function(){
    //pump([
        gulp.src(portal_js_files)
        .pipe(gcat('portal.js'))
        .pipe(strp())
        .pipe(comp({removeSpaces:true}))
        .pipe(gulp.dest('./assets/js/'))
    //]);
});

gulp.task('compile-backend-js',function(){
    //pump([
        gulp.src(backend_js_files)
        .pipe(gcat('backend.js'))
        .pipe(strp())
        .pipe(comp({removeSpaces:true}))
        .pipe(gulp.dest('./assets/js/'))
    //]);
});

gulp.task('watch',function(){
    gulp.watch(frontend_js_files,['compile-frontend-js']);
    gulp.watch(portal_js_files,['compile-portal-js']);
    gulp.watch(backend_js_files,['compile-backend-js']);
    gulp.watch('./dev/scss/*.scss',['apply-scss']);
});

// Default.
gulp.task('default',[]);
