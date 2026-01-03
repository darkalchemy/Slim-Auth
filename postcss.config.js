const purgeImport = require('@fullhuman/postcss-purgecss');
const purgeCSSPlugin = purgeImport.purgeCSSPlugin || purgeImport.default || purgeImport;

module.exports = {
    plugins: [
        require('postcss-preset-env'),
        require('autoprefixer'),
        ...process.env.NODE_ENV === 'production' ? [
            purgeCSSPlugin({
                content: ['./resources/views/**/*.twig']
            })
        ] : []
    ]
}
