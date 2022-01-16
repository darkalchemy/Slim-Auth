const {WebpackManifestPlugin} = require('webpack-manifest-plugin');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin').default;
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const CompressionPlugin = require("compression-webpack-plugin");
const ESLintPlugin = require('eslint-webpack-plugin');
const StylelintPlugin = require('stylelint-webpack-plugin');
const zopfli = require("@gfx/zopfli");
const zlib = require("zlib");
const path = require('path');

let mode = 'development';
if (process.env.NODE_ENV === 'production') {
    mode = 'production'
}
module.exports = {
    mode: mode,
    devtool: 'source-map',
    entry: {
        'default': './resources/scripts/app.js'
    },
    output: {
        filename: '[name].[fullhash].js',
        path: path.resolve(__dirname, './public/resources')
    },
    module: {
        rules: [
            {
                test: /\.(s[ca]|c)ss$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader', 'sass-loader']
            },
            {
                test: /\.js/,
                exclude: /node_modules/,
                use: 'babel-loader',
            }
        ]
    },
    optimization: {
        minimizer: [
            new CssMinimizerPlugin(),
        ],
    },
    plugins: [
        new WebpackManifestPlugin({
            fileName: 'manifest.json',
            publicPath: '',
        }),
        new MiniCssExtractPlugin(
            {
                filename: '[name].[fullhash].css'
            }
        ),
        new CleanWebpackPlugin(),
        new CompressionPlugin({
            filename: "[path][base].gz",
            minRatio: 0.8,
            threshold: 8192,
            compressionOptions: {
                numiterations: 15,
            },
            algorithm(input, compressionOptions, callback) {
                return zopfli.gzip(input, compressionOptions, callback);
            },
        }),
        new CompressionPlugin({
            filename: "[path][base].br",
            algorithm: "brotliCompress",
            compressionOptions: {
                params: {
                    [zlib.constants.BROTLI_PARAM_QUALITY]: 11,
                },
            },
            threshold: 8192,
            minRatio: 0.8,
        }),
        new ESLintPlugin(),
        new StylelintPlugin({
            'fix': true,
            'files': ['./resources/styles/*.css'],
        }),
    ],
};
