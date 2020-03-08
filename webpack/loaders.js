const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const JSLoader = {
    test: /\.js$/,
    exclude: /node_modules/,
    use: {
        loader: 'babel-loader',
        options: {
            presets: ['@babel/preset-env']
        }
    }
};

const ESLintLoader = {
    test: /\.js$/,
    enforce: 'pre',
    exclude: /node_modules/,
    use: {
        loader: 'eslint-loader',
        options: {
            configFile: __dirname + '/.eslintrc'
        }
    }
};


const CSSLoader = {
    test: /\.scss$/,
    use: [
        MiniCssExtractPlugin.loader,
        {
            loader: 'css-loader',
            options: {
                sourceMap: true
            }
        },
        {
            loader: 'postcss-loader',
            options: {
                config: {
                    ctx: {
                        'purgecss': {
                            content: ['./resources/views/**/*.twig'],
                            keyframes: true,
                            whitelist: ['html', 'body']
                        },
                        'postcss-preset-env': {
                            browsers: 'last 2 versions'
                        },
                        'postcss-font-magician': {
                            protocol: 'https:'
                        },
                        'cssnano': {preset: ['default', {discardComments: {removeAll: true}}]}
                    },
                    path: __dirname + '/postcss.config.js'
                }
            }
        },
        {
            loader: 'sass-loader',
            options: {
                sourceMap: true
            }
        }
    ]
};

module.exports = {
    JSLoader: JSLoader,
    ESLintLoader: ESLintLoader,
    CSSLoader: CSSLoader
};
