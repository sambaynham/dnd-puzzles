/**
 * @license
 * Copyright 2018 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */

import summary from 'rollup-plugin-summary';
import terser from '@rollup/plugin-terser';
import resolve from '@rollup/plugin-node-resolve';
import replace from '@rollup/plugin-replace';
import sass from 'rollup-plugin-sass';
import typescript from '@rollup/plugin-typescript';

export default {
    input: 'resources/typescript/app.ts',
    output: {
        file: './public/js/app.js',
        format: 'esm',
    },
    onwarn(warning) {
        if (warning.code !== 'THIS_IS_UNDEFINED') {
            console.error(`(!) ${warning.message}`);
        }
    },
    plugins: [
        typescript(),
        replace({preventAssignment: false, 'Reflect.decorate': 'undefined'}),
        sass({
            output: "./public/css/app.css",
            failOnError: true,
        }),
        resolve(),
        /**
         * This minification setup serves the static site generation.
         * For bundling and minification, check the README.md file.
         */
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
};
