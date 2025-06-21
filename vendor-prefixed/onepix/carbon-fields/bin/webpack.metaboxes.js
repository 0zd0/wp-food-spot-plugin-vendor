/**
 * External dependencies.
 */
const webpack = require( 'webpack' );
const { merge } = require( 'webpack-merge' );

/**
 * Internal dependencies.
 */
const base = require( './webpack.base' );
const paths = require( './paths' );
const wpPackages = require( './wp-packages' );

const config = {
	entry: {
		metaboxes: './packages/metaboxes/index.js'
	},
	output: {
		library: [ 'cf', '[name]' ],
		libraryTarget: 'this'
	},
	externals: {
		// 'react': [ 'cf', 'vendor', 'react' ],
		// 'react-dom': [ 'cf', 'vendor', 'react-dom' ],
		'nanoid': [ 'cf', 'vendor', 'nanoid' ],
		'refract-callbag': [ 'cf', 'vendor', 'refract-callbag' ],
		'callbag-basics': [ 'cf', 'vendor', 'callbag-basics' ],
		'classnames': [ 'cf', 'vendor', 'classnames' ],
		'immer': [ 'cf', 'vendor', 'immer' ],
		'@carbon-fields/core': [ 'cf', 'core' ]
	}
};

module.exports = [
	merge( base, config, {
		output: {
			path: paths.gutenbergBuildPath
		},
		externals: Object.assign( {}, wpPackages.externals, {
			'lodash': [ 'lodash' ]
		} )
	} ),
	merge( base, config, {
		output: {
			path: paths.classicBuildPath
		},
		externals: Object.assign( {}, wpPackages.proxyExternals, {
			'lodash': [ 'cf', 'vendor', 'lodash' ],
			'react-dom': ['ReactDOM'],
			'react': ['React'],
			'@wordpress/element': ['wp', 'element'],
			'@wordpress/data': ['wp', 'data'],
			'@wordpress/i18n': ['wp', 'i18n'],
			'@wordpress/hooks': ['wp', 'hooks'],
			'@wordpress/compose': ['wp', 'compose'],
		} ),
		plugins: [
			new webpack.ProvidePlugin( {
				'wp.element': '@wordpress/element'
			} )
		]
	} )
];
