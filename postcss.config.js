const purgecss = require('@fullhuman/postcss-purgecss');

module.exports = {
    plugins: [
        require('postcss-preset-env'),
        require('autoprefixer'),
        ...process.env.NODE_ENV === 'production' ? [
            purgecss({
                content: ['./resources/views/**/*.twig']
            })
        ] : []
    ]
}
