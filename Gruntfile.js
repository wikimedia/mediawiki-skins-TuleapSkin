/*!
 * Grunt file
 *
 */

/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	const conf = grunt.file.readJSON( 'skin.json' );

	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		eslint: {
			options: {
				cache: true,
				fix: grunt.option( 'fix' )
			},
			all: [
				'**/*.{js,json}',
				'!{vendor,node_modules}/**',
				'!resources/lib/project-sidebar.umd.js'
			]
		},
		stylelint: {
			options: {
				cache: true
			},
			all: [
				'**/*.{css,less}',
				'!node_modules/**',
				'!resources/lib/project-sidebar.css',
				'!vendor/**'
			]
		},
		banana: conf.MessagesDirs,
		watch: {
			files: [
				'<%= eslint.all %>',
				'<%= stylelint.all %>'
			],
			tasks: 'test'
		}
	} );

	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
