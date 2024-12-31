const path = require('path');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const CopyPlugin = require("copy-webpack-plugin");

module.exports = {
  entry: {
    'vendor': './assets/index.js',
  },
  output: {
    path: path.resolve(__dirname, 'public/assets'),
    publicPath: 'auto',
    filename: '[name].[contenthash].js',
    clean: true,
  },
  optimization: {
    minimizer: [new TerserJSPlugin({}), new CssMinimizerPlugin({})],
  },
  performance: {
    maxEntrypointSize: 1024000,
    maxAssetSize: 1024000
  },
  resolve: {
    extensions: ['.js']
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          {
            loader: "css-loader",
              options: {
              url: true,
              esModule: false,
            },
          },
        ],
      },
      {
        test: /\.jpg|\.png/,
        type: 'asset/resource',
      },
      {
        test: /\.(woff|woff2|eot|ttf)(\?.*$|$)/i,
        use: [
          'file-loader',
        ],
      },
    ],
  },
  plugins: [
    new CleanWebpackPlugin(),
    new WebpackManifestPlugin(),
    new MiniCssExtractPlugin({
      ignoreOrder: false
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    }),
    new CopyPlugin({
      patterns: [
        {
          from: "assets/img/logo_icon.png",
          to: "logo_icon.png",
        },
      ],
    }),
  ],
  watchOptions: {
    ignored: ['./node_modules/']
  },
  mode: "development"
};
