import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser  from '@rollup/plugin-terser';
import copy from 'rollup-plugin-copy'
import typescript from '@rollup/plugin-typescript';
import scss from 'rollup-plugin-scss';

const copyConfig = {
  targets: [
  	{
        src: 'resources/images',
        dest: 'public'
    },
    {
        src: 'resources/fonts',
        dest: 'public'
    },
      {
          src: 'resources/audio',
          dest: 'public'
      },
  ]
};

const terserOptions = {
    ecma: 2021,
    module: true,
    warnings: true,
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
            dir: './public/js/',
            format: 'es',
        },

        plugins: [
            copy(copyConfig),
            typescript(),
            resolve(),
            summary(),
        ],
        preserveEntrySignatures: false,
    },
    {
        input: 'resources/sass/critical.scss',
        output: {
            dir: './public/css/'
        },
        plugins: [
            scss({
                fileName: 'app.css',
                outputStyle: 'compressed',
                watch: [
                    'resources/sass/critical.scss',
                    'resources/sass/critical/**/*.scss'
                ]
            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/puzzles/lightforge.scss',
        output: {
            dir: './public/css/puzzles'
        },
        plugins: [
            scss({
                fileName: 'lightforge.css',
                outputStyle: 'compressed',
                watch: ['resources/sass/puzzles/lightforge.scss']

            }),
            summary()
        ]
    },
    {
        input: 'resources/sass/puzzles/hammer.scss',
        output: {
            dir: './public/css/puzzles'
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
