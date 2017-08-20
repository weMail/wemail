const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const postcssrc = require('./.postcssrc.js').plugins.autoprefixer;

function srcDir(dir) {
    return path.join(__dirname, 'assets/src/', dir);
}

function jsDist(filename) {
    return path.join(__dirname, 'assets/js/', filename + '.js');
}

let plugins = [
    new webpack.ProvidePlugin({
        $: "jQuery"
    }),
    // new webpack.optimize.UglifyJsPlugin({
    //     sourceMap: true,
    //     compress: {
    //         warnings: true
    //     }
    // }),

    // new webpack.DefinePlugin({
    //   'process.env': {
    //     NODE_ENV: JSON.stringify('production')
    //   }
    // })
];

module.exports = {
    resolve: {
        modules: [
            path.resolve('./node_modules'),
            path.resolve(path.join(__dirname, 'assets/src/'))
        ]
    },

    entry: {
        vendor: jsDist('vendor'),
        wemail: jsDist('wemail'),
        admin: jsDist('admin'),
        Overview: jsDist('Overview'),
        Campaigns: jsDist('Campaigns'),
        Subscribers: jsDist('Subscribers'),
        Forms: jsDist('Forms'),
        Lists: jsDist('Lists'),
        Settings: jsDist('Settings'),
        'directives-and-mixins': jsDist('directives-and-mixins'),
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
                    formatter: require('eslint-friendly-formatter')
                },
                exclude: /node_modules/
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                // options: vueLoaderConfig
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/
            },
        ]
    }
};
