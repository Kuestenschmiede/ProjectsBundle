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
    'c4g-vendor-ckeditor': './Resources/public/vendor/js/c4g-vendor-ckeditor.js',
    'c4g-vendor-fontawesome': './Resources/public/vendor/js/c4g-vendor-fontawesome.js',
    'c4g-vendor-magnificpopup': './Resources/public/vendor/js/c4g-vendor-magnificpopup.js'
  },
  mode: "development",
  output: {
    filename: '[name].js',
    path: path.resolve('./Resources/public/dist/js/'),
    chunkFilename: '[name].bundle.js',
    publicPath: "bundles/con4gisprojects/dist/js"
  },
  devtool: "inline-source-map",
  resolve: {
    modules: [
      'node_modules',
      'Resources/public/vendor/js'
    ],
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
  }
};

module.exports = config;