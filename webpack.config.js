const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const postcssrc = require('./.postcssrc.js').plugins.autoprefixer;
const StyleLintPlugin = require('stylelint-webpack-plugin');
const eslintFriendlyFormatter = require('eslint-friendly-formatter');
const postcssFlexbugsFixes = require('postcss-flexbugs-fixes');
const postcssCssnext = require('postcss-cssnext');

function srcDir(dir) {
    return path.join(__dirname, 'assets/src/', dir);
}

function jsSrc(filename, subDir = '') {
    return path.join(srcDir('js'), subDir, `${filename}.js`);
}

function sassSrc(filename) {
    return path.join(srcDir('scss'), `${filename}.scss`);
}

function sassResources() {
    return [
        sassSrc('_variables'), sassSrc('_mixins')
    ];
}

function isProduction() {
    return process.env.NODE_ENV === 'production';
}

function cssLoaders(options) {
    options = options || {};

    const cssLoader = {
        loader: 'css-loader',
        options: {
            minimize: isProduction(),
            sourceMap: options.sourceMap
        }
    };

    // generate loader string to be used with extract text plugin
    function generateLoaders(loader, loaderOptions) {
        const loaders = [cssLoader];

        if (loader) {
            loaders.push({
                loader: `${loader}-loader`,
                options: Object.assign({}, loaderOptions, {
                    sourceMap: options.sourceMap
                })
            });
        }

        // Extract CSS when that option is specified
        // (which is the case during production build)
        if (options.extract) {
            return ExtractTextPlugin.extract({
                use: loaders,
                fallback: 'vue-style-loader'
            });
        }

        return ['vue-style-loader'].concat(loaders);
    }

    // https://vue-loader.vuejs.org/en/configurations/extract-css.html
    return {
        scss: generateLoaders('sass').concat({
            loader: 'sass-resources-loader',
            options: {
                resources: sassResources()
            }
        })
    };
}

const vueLoaderConfig = {
    loaders: cssLoaders({
        sourceMap: false,
        extract: false
    })
};

const plugins = [
    new webpack.ProvidePlugin({
        $: 'jQuery'
    }),

    new ExtractTextPlugin({
        filename: '../css/[name].css'
    }),

    new StyleLintPlugin({
        configFile: path.resolve('./.stylelintrc'),
        files: [
            'assets/src/sass/**/*.scss', 'assets/src/js/**/*.vue'
        ]
    })
];

if (isProduction()) {
    plugins.push(new webpack.optimize.UglifyJsPlugin({
        sourceMap: true,
        compress: {
            warnings: false
        }
    }));

    plugins.push(new webpack.DefinePlugin({
        'process.env': {
            NODE_ENV: JSON.stringify('production')
        }
    }));
}

module.exports = {
    resolve: {
        modules: [
            path.resolve('./node_modules'),
            path.resolve(path.join(__dirname, 'assets/src/'))
        ]
    },

    entry: {
        vendor: jsSrc('vendor'),
        wemail: jsSrc('wemail'),
        common: jsSrc('common'),
        admin: jsSrc('admin'),
        Overview: jsSrc('Overview', 'routes/Overview'),
        Campaigns: jsSrc('Campaigns', 'routes/Campaigns'),
        Subscribers: jsSrc('Subscribers', 'routes/Subscribers'),
        Subscriber: jsSrc('Subscriber', 'routes/Subscribers'),
        Forms: jsSrc('Forms', 'routes/Forms'),
        Lists: jsSrc('Lists', 'routes/Lists'),
        Settings: jsSrc('Settings', 'routes/Settings'),
        404: jsSrc('FourZeroFour', 'routes/FourZeroFour')
    },

    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, './assets/js/')
    },

    externals: {
        jQuery: 'jQuery'
    },

    plugins,

    module: {
        rules: [
            {
                test: /\.(js|vue)$/,
                loader: 'eslint-loader',
                enforce: 'pre',
                include: [srcDir('js'), srcDir('vue')],
                options: {
                    formatter: eslintFriendlyFormatter
                },
                exclude: /node_modules/
            },
            {
                test: /\.scss$/,
                exclude: /node_modules/,
                use: ExtractTextPlugin.extract({
                    use: [
                        {
                            loader: 'css-loader',
                            query: {
                                modules: false,
                                sourceMap: false,
                                localIdentName: '[name]',
                                minimize: isProduction()
                            }
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                plugins: (loader) => [
                                    postcssFlexbugsFixes,
                                    postcssCssnext(postcssrc)
                                ]
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                outputStyle: 'expanded'
                            }
                        },
                        {
                            loader: 'sass-resources-loader',
                            options: {
                                resources: sassResources()
                            }
                        }
                    ],
                    fallback: 'style-loader'
                })
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: vueLoaderConfig
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            }
        ]
    }
};
