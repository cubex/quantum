const path = require('path');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

const filerCfg = {
  entry: {
    'filer': './_resources_src/filer.js'
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
    ]
  },
  performance: {hints: false},
  optimization: {
    minimizer: [
      new TerserWebpackPlugin(
        {
          sourceMap: false,
        }
      )
    ]
  },

  plugins: [
    new CleanWebpackPlugin(),
  ],
  output: {
    path: path.resolve(__dirname, '_resources/'), //directory for output files
    filename: '[name].min.js' //using [name] will create a bundle with same file name as source
  },
};

module.exports = [filerCfg, ckeditorCfg];
