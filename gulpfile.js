// ─── Importacion De Dependencias ─────────────────────────────────────────────
// Modulos de Node.js
import path, { join } from 'path';
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

// ─── Compilar Todos Los Archivos JS ──────────────────────────────────────────
export function compilarJS(done) {
    src(paths.js)
        .pipe(terser())
        .pipe(dest('./public/build/js'))
    done()
}

// ─── Obtención De Rutas De Las Imagenes ──────────────────────────────────────
export async function rutasImagenes(done) {
    const srcDir = './src/img';
    const buildDir = './public/build/img';
    const images = await glob('./src/img/**/*')

    images.forEach(file => {
        const relativePath = path.relative(srcDir, path.dirname(file));
        const outputSubDir = path.join(buildDir, relativePath);

        // Enviar constantes a la funcion de optimizar imagenes
        procesarImagenes(file, outputSubDir);
    });
    done()
}

// ─── Optimización De Imagenes ────────────────────────────────────────────────
function procesarImagenes(file, outputSubDir) {
    
    // Creacion del directorio si no existe
    if(!fs.existsSync(outputSubDir)) {
        fs.mkdirSync(outputSubDir, { recursive: true })
    }

    const baseName = path.basename(file, path.extname(file));
    const extName = path.extname(file);

    // Pasar las imagenes svg directamente al directorio nuevo
    if(extName.toLowerCase() === '.svg') {
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`);

        fs.copyFileSync(file, outputFile);
    } else {
        const outputFile = path.join(outputSubDir, `${baseName}${extName}`);
        const outputFileWebp = path.join(outputSubDir, `${baseName}.webp`);
        const outputFileAvif = path.join(outputSubDir, `${baseName}.avif`);
        const options = { quality: 80 };

        sharp(file).jpeg(options).toFile(outputFile);
        sharp(file).webp(options).toFile(outputFileWebp);
        sharp(file).avif().toFile(outputFileAvif);
    };
}

// ─── Vigilar Por Cambios ─────────────────────────────────────────────
export function watchFunciones() {
    watch(paths.scss, compilarSCSS);
    watch( paths.js, compilarJS );
    watch('src/img/**/*.{png,jpg}', rutasImagenes);
}

// ─── Exportar Funciones ──────────────────────────────────────────────
export default series( compilarJS, compilarSCSS, rutasImagenes, watchFunciones );