import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser  from '@rollup/plugin-terser';
import copy from 'rollup-plugin-copy'
import typescript from '@rollup/plugin-typescript';
import scss from 'rollup-plugin-scss';
import clean from '@rollup-extras/plugin-clean';

const copyConfig = {
  targets: [
  	{
        src: 'resources/images',
        dest: 'public/dist'
    },
    {
        src: 'resources/fonts',
        dest: 'public/dist'
    },
    {
        src: 'resources/audio',
        dest: 'public/dist'
    },
  ]
};

const terserOptions = {
    ecma: 2021,
    module: true,
    warnings: true,
    keep_fnames: true,
    mangle: {
        properties: {
            regex: /^__/,
        },
    },
}

const config = [
    {

        input: 'resources/typescript/app.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es'
        },

        plugins: [
            copy(copyConfig),
            typescript(),
            resolve(),
            clean(),
            summary(),
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/sass/critical.scss',
        output: {
            dir: './public/dist/css/'
        },
        plugins: [
            scss({
                fileName: 'critical.css',
                outputStyle: 'compressed',
                watch: [
                    'resources/sass/critical.scss',
                    'resources/sass/critical/'
                ],
            }),

            summary()
        ]
    },
    {
        input: 'resources/typescript/puzzles/hammer/hammerBoot.ts',
        output: {
            dir: './public/dist/js/',
            format: 'es',
        },

        plugins: [
            typescript(),
            resolve()
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/sass/puzzles/hammer.scss',
        output: {
            dir: './public/dist/css/puzzles'
        },
        plugins: [
            scss({
                fileName: 'hammer.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/hammer.scss']
            }),
            summary()
        ]
    }

];



if (process.env.NODE_ENV !== 'development') {
  config[0].plugins.push(terser(terserOptions));
}

export default config;
