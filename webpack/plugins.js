const path = require('path');
const _MiniCssExtractPlugin = require('mini-css-extract-plugin');
const _StyleLintPlugin = require('stylelint-webpack-plugin');
const _CompressionPlugin = require('compression-webpack-plugin');
const zopfli = require('@gfx/zopfli');

const MiniCssExtractPlugin = new _MiniCssExtractPlugin({
    filename: '[name].[hash:8].css',
    chunkFilename: '[id].[hash:8].css'
});

const StyleLintPlugin = new _StyleLintPlugin({
    configFile: path.resolve(__dirname, 'stylelint.config.js'),
    context: path.resolve(__dirname, '../resources/styles'),
    files: 'app.scss',
    emitErrors: true,
    failOnError: true,
    quiet: false
});

const CompressionPlugin = new _CompressionPlugin({
    filename: '[path].gz[query]',
    compressionOptions: {
        numiterations: 15,
        blocksplitting: true
    },
    algorithm(input, compressionOptions, callback) {
        return zopfli.gzip(input, compressionOptions, callback);
    },
    test: /\.js$|\.css$$/,
    threshold: 10240,
    minRatio: 0.75,
    deleteOriginalAssets: false
});

module.exports = {
    MiniCssExtractPlugin: MiniCssExtractPlugin,
    StyleLintPlugin: StyleLintPlugin,
    CompressionPlugin: CompressionPlugin
};