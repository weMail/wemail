/**
 * The block's @wordpress/* imports are not installed: the build maps them to
 * WordPress's own scripts (window.wp.*) and lists them in index.asset.php.
 * These declarations stop editors reporting "Cannot find module" for them.
 */
declare module '@wordpress/block-editor';
declare module '@wordpress/blocks';
declare module '@wordpress/components';
declare module '@wordpress/element';
declare module '@wordpress/i18n';
