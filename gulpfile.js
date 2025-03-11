// ─── Importacion De Dependencias ─────────────────────────────────────────────
// Modulos de Node.js
import path from 'path';
import fs from 'fs';

// Dependencias
import { glob } from 'glob';
import { src, dest, watch, series } from 'gulp';
import dartSass from 'sass';
import gulpSass from 'gulp-sass';
const sass = gulpSass(dartSass);
import terser from 'gulp-terser';
import sharp from 'sharp';

// ─── Rutas Definidas ─────────────────────────────────────────────────────────
const paths = {
    scss: 'src/scss/**/*.scss',
    js: 'src/js/**/*.js'
}

// ─── Compilar Sass A Css ─────────────────────────────────────────────────────
export function compilarSCSS(done) {
    src(paths.scss, { sourcemaps: true })
        .pipe(sass({
            outputStyle: 'compressed'
        })
        .on('error', sass.logError))
        .pipe(dest(
            './public/build/css', { sourcemaps: '.' }
        ));
    done()
}