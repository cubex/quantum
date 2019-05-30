const path = require('path');
const webpack = require('webpack');
const {bundler, styles} = require('@ckeditor/ckeditor5-dev-utils');
const CKEditorWebpackPlugin = require('@ckeditor/ckeditor5-dev-webpack-plugin');
const TerserWebpackPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

const filerCfg = {
  entry: {
    'filer': './src/Modules/Upload/Controllers/_resources/filer.js'
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
  output: {
    path: path.resolve(__dirname, 'src/Modules/Upload/Controllers/_resources/'), //directory for output files
    filename: '[name].min.js' //using [name] will create a bundle with same file name as source
  },
};

const ckeditorCfg = {
  entry: {
    'ckeditor': './src/Base/Components/Ckeditor/_resources_src/ckeditor.js'
  },
  output: {
    path: path.resolve(__dirname, 'src/Base/Components/Ckeditor/_resources/plugin/'), //directory for output files
    filename: '[name].min.js' //using [name] will create a bundle with same file name as source
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
    new CKEditorWebpackPlugin(
      {
        // UI language. Language codes follow the https://en.wikipedia.org/wiki/ISO_639-1 format.
        // When changing the built-in language, remember to also change it in the editor's configuration (src/shared.js).
        language: 'en',
        additionalLanguages: 'all'
      }
    ),
    new webpack.BannerPlugin(
      {
        banner: bundler.getLicenseBanner(),
        raw: true
      }
    ),
    new MiniCssExtractPlugin(
      {
        filename: '[name].min.css'
      }
    )
  ],

  module: {
    rules: [
      {
        test: /\.svg$/,
        use: ['raw-loader']
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'postcss-loader',
            options: styles.getPostCssConfig(
              {
                themeImporter: {
                  themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                },
                minify: true
              }
            )
          },
        ]
      }
    ]
  }
};

module.exports = [filerCfg, ckeditorCfg];
