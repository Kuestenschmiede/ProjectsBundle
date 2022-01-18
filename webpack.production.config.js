/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const webpack = require("webpack");

var path = require('path');
var config = {
  entry: {
    'c4g-vendor-trix': './src/Resources/public/vendor/js/c4g-vendor-trix.js',
    'c4g-vendor-minisearch': './src/Resources/public/vendor/js/c4g-vendor-minisearch.js',
    'c4g-vendor-jquery': './src/Resources/public/vendor/js/c4g-vendor-jquery.js',
    'c4g-vendor-datepicker': './src/Resources/public/vendor/js/c4g-vendor-datepicker.js'
  },
  mode: 'production',
  output: {
    filename: '[name].js',
    path: path.resolve('./src/Resources/public/dist/js'),
    chunkFilename: '[name].bundle.[contenthash].js',
    publicPath: 'bundles/con4gisprojects/dist/js/'
  },
  resolve: {
    modules: ['node_modules', 'src/Resources/public/vendor/js'],
    extensions: ['.js', '.ts', '.svg']
  },
  module: {
    rules: [
      {
        include: [
          path.resolve('.'),
          path.resolve('./src/Resources/public/vendor/js/')
        ],
      },
      {
        test: /\.svg$/,
        loader: 'svg-inline-loader'
      },
      {
        test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
        use: [ 'raw-loader' ]
      },
      {
        test: /\.(eot|woff|ttf)/,
        loader: 'url-loader'
      },
      {
        test: /\.png$/,
        loader: 'file-loader'
      },
      {
        test: /\.css$/i,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  optimization: {
    minimize: true,
    removeAvailableModules: true,
    flagIncludedChunks: true,
    usedExports: true,
    concatenateModules: true,
    sideEffects: false,
    chunkIds: 'named',
    moduleIds: 'named'
  }
};

module.exports = config;