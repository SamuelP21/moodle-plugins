/* global process */
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const path = require('path');
const fs = require('fs');
const mkdirp = require('mkdirp');

   // Obtener la ruta absoluta del directorio actual
   const BASE_PATH = process.cwd();

// Tarea para compilar SASS a CSS
gulp.task('styles', function() {
    // Rutas de origen y destino
    const srcPaths = [
        path.join(BASE_PATH, 'src/scss/app.scss')
    ];
    const destPath = path.join(BASE_PATH, 'dist/css');

    return gulp.src(srcPaths, { allowEmpty: true })
        .pipe(sass({
            includePaths: [
                path.join(BASE_PATH, 'src/scss'),
                path.join(BASE_PATH, 'src/scss/courses_glide'),
                path.join(BASE_PATH, 'src/scss/base')
            ],
            outputStyle: 'expanded',
            sourceComments: true,
            sourceMap: true,
            precision: 10
        }).on('error', sass.logError))
        .pipe(concat('app.css')) // Concatenar todo en un único archivo
        .pipe(gulp.dest(destPath));
});

// Tarea para observar cambios
gulp.task('watch', function() {
    const watchPaths = [
        path.join(BASE_PATH, 'src/scss/**/*.scss')
    ];
    return gulp.watch(watchPaths, gulp.series('styles'));
});

   // Tarea para desarrollo
   gulp.task('dev', gulp.series('styles', 'watch'));

// Tarea para producción
gulp.task('build', gulp.series('styles'));

// Tarea por defecto - Se ejecuta al correr 'gulp' sin argumentos
gulp.task('default', gulp.series('styles', 'watch'));

// Crear directorio dist/css si no existe
const distPath = path.join(BASE_PATH, 'dist/css');

if (!fs.existsSync(distPath)) {
    console.log('Creando directorio:', distPath);
    mkdirp.sync(distPath);
}