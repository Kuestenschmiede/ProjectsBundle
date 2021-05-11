/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const webpack = require("webpack");
var path = require('path');
var config = {
  entry: {
    'c4g-vendor-ckeditor': './Resources/public/vendor/js/c4g-vendor-ckeditor.js',
    'c4g-vendor-fontawesome': './Resources/public/vendor/js/c4g-vendor-fontawesome.js',
    'c4g-vendor-magnificpopup': './Resources/public/vendor/js/c4g-vendor-magnificpopup.js'
  },
  mode: "production",
  output: {
    filename: '[name].js',
    path: path.resolve('./Resources/public/dist/js'),
    chunkFilename: '[name].bundle.[contenthash].js',
    publicPath: "bundles/con4gismaps/dist/js/"
  },
  resolve: {
    modules: ['node_modules', 'Resources/public/vendor/js'],
    extensions: ['.js']
  },
  module: {
    rules: [
      {
        include: [
          path.resolve('.'),
          path.resolve('./Resources/public/vendor/js/')
        ],
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
    chunkIds: "named",
    moduleIds: "named"
  }
};

module.exports = config;