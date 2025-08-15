import resolve from '@rollup/plugin-node-resolve';
import summary from 'rollup-plugin-summary';
import terser  from '@rollup/plugin-terser';
import copy from 'rollup-plugin-copy'
import typescript from '@rollup/plugin-typescript';
import sass from 'rollup-plugin-sass';

const copyConfig = {

  targets: [
  	{ src: 'resources/images', dest: 'public' },
        { src: 'resources/fonts', dest: 'public' },
  ]
};


// The main JavaScript bundle for modern browsers that support

// JavaScript modules and other ES2015+ features.

const config = {

  input: 'resources/typescript/app.ts',

  output: {

    dir: './public/js/',
    format: 'es',

  },

  plugins: [
    copy(copyConfig),
    typescript(),
    sass({
        output: "./public/css/app.css",
    	failOnError: true,
    }),
    resolve(),
    terser({
	    ecma: 2021,
            module: true,
            warnings: true,
            mangle: {
        	    properties: {
                        regex: /^__/,
                    },
                },
            }),
            summary(),

  ],

  preserveEntrySignatures: false,

};


if (process.env.NODE_ENV !== 'development') {

  config.plugins.push(terser());

}


export default config;
