var webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const MinifyPlugin = require("babel-minify-webpack-plugin");
var UnminifiedWebpackPlugin = require('unminified-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: ['./themes/qmtbcsimple/javascript/scripts.js','./themes/qmtbcsimple/javascript/auth.js'],
  output: {
    path: __dirname+'/themes/qmtbcsimple/javascript/',
    filename: "bundle.min.js",
    hashFunction: 'sha256',
  },
  module: {
    rules: [
      {
        test: /\.(scss|css)$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              hmr: process.env.NODE_ENV === 'development',
              minimize: {
                safe: false
              },
              sourceMap: true
            }
          }
        ]
      },
      {test: /\.(gif|png|jpg|svg|cur)$/, use: 'file-loader?emitFile=false&publicPath=../images/&name=[name].[ext]' },
      {test: /\.(eot|ttf|woff|woff2|otf|ttc)$/, use: 'file-loader?emitFile=false&publicPath=../webfonts/&name=[name].[ext]' }
    ]
  },
  resolve: {
    alias: {
      jquery: "jquery/src/jquery"
    }
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "../css/styles.min.css",
      chunkFilename: '../css/styles.css',
    }),
    // new ExtractTextPlugin("../css/styles.css"),
    // new UnminifiedWebpackPlugin(),
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery",
      "window.jQuery": "jquery"
    }),
  ],
  node: {
    // fs: "empty"
  },
  watch: true,
  optimization: {
      minimize: false
  }
};
 