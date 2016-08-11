goog.provide('jmikola.AssetsHelper.Helper');
goog.provide('jmikola.AssetsHelper.InvalidPackageError');

goog.require('jmikola.AssetsHelper.Package');
goog.require('jmikola.AssetsHelper.PathPackage');
goog.require('jmikola.AssetsHelper.UrlPackage');
goog.require('goog.array');

/**
 * @constructor
 */
jmikola.AssetsHelper.Helper = function() {
    this.basePath_ = '/';
    this.defaultPackage_ = null;
    this.namedPackages_ = [];
};
goog.addSingletonGetter(jmikola.AssetsHelper.Helper);

/**
 * Create an appropriate package instance from a config object.
 *
 * @param {Object} config
 * @return {jmikola.AssetsHelper.Package}
 */
jmikola.AssetsHelper.Helper.prototype.createPackage_ = function(config) {
    if (config.hasOwnProperty('baseUrls')) {
        return new jmikola.AssetsHelper.UrlPackage(config.baseUrls, config.version, config.format);
    }
    if (config.hasOwnProperty('version')) {
        return new jmikola.AssetsHelper.PathPackage(this.basePath_, config.version, config.format);
    }
    return this.createPackage_(config['https:' === window.location.protocol ? 'ssl' : 'http']);
}

/**
 * @param {string} basePath               The base path to be prepended to relative paths
 * @param {Object} defaultPackageConfig   Default package configuration
 * @param {Array} namedPackageConfigs    Named package configurations by name
 */
jmikola.AssetsHelper.Helper.prototype.init = function(basePath, defaultPackageConfig, namedPackageConfigs) {
    this.basePath_ = basePath;
    this.defaultPackage_ = this.createPackage_(defaultPackageConfig);
    this.namedPackages_ = goog.array.map(namedPackageConfigs, function(v) { return jmikola.AssetsHelper.Helper.prototype.createPackage_(v);});
};

/**
 * Returns an asset package.
 *
 * @param {string=} opt_name The name of the package or null for the default package
 * @return {jmikola.AssetsHelper.Package} An asset package
 * @throws {jmikola.AssetsHelper.InvalidPackageError} If there is no package by that name
 */
jmikola.AssetsHelper.Helper.prototype.getPackage_ = function(opt_name) {
    if (undefined === opt_name || null === opt_name) {
        return this.defaultPackage_;
    }

    if (!this.namedPackages_.hasOwnProperty(opt_name)) {
        throw new jmikola.AssetsHelper.InvalidPackageError(opt_name);
    }

    return this.namedPackages_[opt_name];
};

/**
 * Returns the public path.
 *
 * Absolute paths (i.e. http://...) are returned unmodified.
 *
 * @param {string}    path            A public path
 * @param {string=}   opt_packageName The name of the asset package to use
 *
 * @return {string} A public path which takes into account the base path and URL path
 */
jmikola.AssetsHelper.Helper.prototype.getUrl = function(path, opt_packageName) {
    return this.getPackage_(opt_packageName).getUrl(path);
};

/**
 * Gets the version to add to public URL.
 *
 * @param {string=} opt_packageName A package name
 * @return {string} The current version
 */
jmikola.AssetsHelper.Helper.prototype.getVersion = function(opt_packageName) {
   return this.getPackage_(opt_packageName).getVersion();
};


/**
 * Custom error type for invalid package name references.
 *
 * @constructor
 * @param {string} name
 * @extends Error
 */
jmikola.AssetsHelper.InvalidPackageError = function(name) {
    this.name = "InvalidPackageError";
    this.message = 'There is no "' + name + '" asset package.';
}
goog.inherits(jmikola.AssetsHelper.InvalidPackageError, Error);

