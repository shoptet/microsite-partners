var BundleTracker = require('webpack-bundle-tracker');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var path = require('path');
var webpack = require('webpack');
var autoprefixer = require('autoprefixer');

var resolve = path.resolve.bind(path, __dirname);

var extractTextPlugin;
var fileLoaderPath;
var output;

output = {
  path: resolve('assets/'),
  filename: '[name].js',
};
fileLoaderPath = 'file-loader?name=[name].[ext]';
extractTextPlugin = new ExtractTextPlugin('[name].css');

var bundleTrackerPlugin = new BundleTracker({
  filename: 'webpack-bundle.json',
});

var commonsChunkPlugin = new webpack.optimize.CommonsChunkPlugin({
  names: 'vendor',
});

var occurenceOrderPlugin = new webpack.optimize.OccurrenceOrderPlugin();

var environmentPlugin = new webpack.DefinePlugin({
  'process.env': {
    NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development'),
  },
});

var providePlugin = new webpack.ProvidePlugin({
  $: 'jquery',
  jQuery: 'jquery',
  'window.jQuery': 'jquery',
  Popper: ['popper.js', 'default'],
  SVGInjector: 'svg-injector-2',
});

var config = {
  entry: {
    main: './static/js/main.js',
    vendor: [
      'babel-es6-polyfill',
      'bootstrap',
      'jquery',
      'svg-injector-2',
    ],
  },
  output: output,
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
      },
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: {
                'sourceMap': true,
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                'sourceMap': true,
                'plugins': function () {
                  return [autoprefixer];
                },
              },
            },
            {
              loader: 'sass-loader',
              options: {
                'sourceMap': true,
              },
            },
          ],
        }),
      },
      {
        test: /\.(eot|otf|png|svg|jpg|ttf|woff|woff2)(\?v=[0-9.]+)?$/,
        use: [
          {
            loader: fileLoaderPath,
          }
        ],
        include: [
          resolve('node_modules'),
          resolve('static/fonts'),
          resolve('static/images'),
        ],
      },
    ],
  },
  plugins: [
    bundleTrackerPlugin,
    commonsChunkPlugin,
    environmentPlugin,
    extractTextPlugin,
    occurenceOrderPlugin,
    providePlugin,
  ],
  resolve: {
    alias: {
      'jquery': resolve('node_modules/jquery/dist/jquery.js'),
    },
  },
};

module.exports = config;
