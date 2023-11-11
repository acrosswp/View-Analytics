/**
 * External dependencies
 */
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const wpPot = require('wp-pot');

var custom_module = {
  plugins: [new MiniCssExtractPlugin()],
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader'
        ]
      }
    ],
  },
};

var script_output = {
  output: {
    path: path.resolve( process.cwd(), 'assets/dist', 'js' ),
		filename: '[name].js',
		chunkFilename: '[name].js',
  },
};

var style_output = {
  output: {
    path: path.resolve( process.cwd(), 'assets/dist', 'css' ),
		filename: '[name].[contenthash].css',
    chunkFilename: '[name].[contenthash].css',
  },
};

var frontend_script = Object.assign({}, script_output, {
  entry: {
      'frontend-script': [
        './assets/src/frontend/js/index.js'
      ],
  },
});


var frontend_style = Object.assign({}, custom_module, style_output, {
  entry: {
      'frontend-style': [
        './assets/src/frontend/css/index.js'
      ],
  },
});


//// POT file.
wpPot( {
	package: 'View Analytics',
	domain: 'view-analytics',
	destFile: 'languages/view-analytics.pot',
	relativeTo: './',
	src: [ './**/*.php' ],
	bugReport: 'https://github.com/acrosswp/view-analytics/issues'
} );

// Return Array of Configurations
module.exports = [
  frontend_style,
  frontend_script
];