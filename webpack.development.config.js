/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

const webpack = require("webpack");

var path = require('path');
var config = {
  entry: {
    'c4g-vendor-trix': './src/Resources/public/vendor/js/c4g-vendor-trix.js',
    'c4g-vendor-minisearch': './src/Resources/public/vendor/js/c4g-vendor-minisearch.js',
    'c4g-vendor-jquery': './src/Resources/public/vendor/js/c4g-vendor-jquery.js'
  },
  mode: "development",
  output: {
    filename: '[name].js',
    path: path.resolve('./src/Resources/public/dist/js/'),
    chunkFilename: '[name].bundle.js',
    publicPath: "bundles/src/con4gisprojects/dist/js"
  },
  devtool: "inline-source-map",
  resolve: {
    modules: [
      'node_modules',
      'src/Resources/public/vendor/js'
    ],
    alias: {
      'parchment': path.resolve(__dirname, 'node_modules/parchment/src/parchment.ts'),
      'quill$': path.resolve(__dirname, 'node_modules/quill/quill.js'),
    },
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
        test: /\.css$/i,
        use: ['style-loader', 'css-loader'],
      },
      {
        test: /\.svg$/,
        loader: 'svg-inline-loader'
      },
      {
        test: /\.(eot|woff|ttf)/,
        loader: 'url-loader'
      },
      {
        test: /\.png$/,
        loader: 'file-loader'
      }
    ]
  }
};

module.exports = config;