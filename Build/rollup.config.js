import resolve from '@rollup/plugin-node-resolve';
import { terser } from 'rollup-plugin-terser';
import cleanup from 'rollup-plugin-cleanup';

export default {
  input: ['./Sources/JavaScript/FmFilterCheckbox.js'],
  output: {
    file: './../Resources/Public/JavaScript/Dist/FilterComponents.min.js',
    format: 'iife',
    name: 'fromesComponents',
    plugins: [terser()]
  },
  plugins: [
    resolve({
      customResolveOptions: {
        moduleDirectories: { nodeModules: 'node_modules' }
      }
    }),
    cleanup({
      comments: 'none'
    })
  ]
};
