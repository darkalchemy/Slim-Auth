module.exports = (ctx) => ({
    map: ctx.webpack.mode === 'production',
    plugins: {
        'postcss-import': {root: ctx.file.dirname},
        'postcss-nested': {},
        '@fullhuman/postcss-purgecss': ctx.webpack.mode === 'production' ? ctx.options.purgecss : false,
        'autoprefixer': {},
        'postcss-preset-env': ctx.options['postcss-preset-env'],
        'postcss-font-magician': ctx.options['postcss-font-magician'],
        'cssnano': ctx.webpack.mode === 'production' ? ctx.options.cssnano : false
    }
});
