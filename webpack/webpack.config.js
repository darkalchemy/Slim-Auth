const path = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const loaders = require('./loaders');
const plugins = require('./plugins');

module.exports = {
    entry: {
        'default': './resources/scripts/app.js'
    },
    output: {
        filename: '[name].[chunkhash:8].js',
        path: path.resolve(__dirname, '../public/resources')
    },
    module: {
        rules: [
            loaders.CSSLoader,
            loaders.JSLoader,
            loaders.ESLintLoader
        ]
    },
    performance: {
        maxEntrypointSize: 1024000,
        maxAssetSize: 1024000
    },
    plugins: [
        new CleanWebpackPlugin(),
        new ManifestPlugin(),
        plugins.StyleLintPlugin,
        plugins.MiniCssExtractPlugin,
        plugins.CompressionPlugin,
    ],
    watchOptions: {
        ignored: ['./node_modules/']
    },
    mode: 'development'
};