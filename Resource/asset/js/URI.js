/**
 * Part of SMS Ajax Image.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

;(function($)
{
	"use strict";

	/**
	 * Uri object.
	 *
	 * @param {string} uri URI string.
	 *
	 * @constructor
	 */
	var Class = window.URI = function(uri)
	{
		this.parse(uri);
	};

	/**
	 * Parse uri string.
	 *
	 * @param {string} uri URI string.
	 */
	Class.prototype.parse = function(uri)
	{
		var parser = document.createElement('a');

		parser.href = uri;

		this.scheme = parser.protocol;
		this.username = parser.username;
		this.password = parser.password;
		this.host = parser.hostname;
		this.port = parser.port;
		this.path = parser.pathname;
		this.query = parseQueryString(parser.search);
		this.hash = parser.hash;
	};

	/**
	 * Set Query.
	 *
	 * @param {Object} queries Query object.
	 */
	Class.prototype.setQuery = function(queries)
	{
		this.query = queries;
	};

	/**
	 * Set query value
	 *
	 * @param {string} key
	 * @param {string} value
	 */
	Class.prototype.setVar = function(key, value)
	{
		this.query[key] = value;
	};

	/**
	 * Get query value
	 *
	 * @param {string} key
	 * @param {string} defaultVal
	 *
	 * @returns {*}
	 */
	Class.prototype.getVar = function(key, defaultVal)
	{
		return this.query[key] || defaultVal;
	};

	/**
	 * Make uri To string.
	 *
	 * @returns {string}
	 */
	Class.prototype.toString = function(parts)
	{
		var uri = '';
		var query = $.param(this.query);

		parts = parts || ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'];

		uri += parts.indexOf('scheme') != -1 ? (this.scheme ? this.scheme + '//' : '') : '';
		uri += parts.indexOf('user') != -1 ? this.username : '';
		uri += parts.indexOf('pass') != -1 ? (this.password ? ':' : '') + this.password + (this.username ? '@' : '') : '';
		uri += parts.indexOf('host') != -1 ? this.host : '';
		uri += parts.indexOf('port') != -1 ? (this.port ? ':' : '') + this.port : '';
		uri += parts.indexOf('path') != -1 ? this.path : '';
		uri += parts.indexOf('query') != -1 ? (query ? '?' + query : '') : '';
		uri += parts.indexOf('fragment') != -1 ? (this.hash ? '#' + this.hash : '') : '';

		return uri;
	};

	/**
	 * Parse http query.
	 *
	 * @param queryString
	 *
	 * @private
	 * @returns {Object}
	 */
	var parseQueryString = function(queryString)
	{
		var params = {},
			queries,
			temp,
			i,
			l;

		queryString = queryString.substring(1);

		// Split into key/value pairs
		queries = queryString.split("&");

		// Convert the array of strings into an object
		for ( i = 0, l = queries.length; i < l; i++ )
		{
			if (! queries[i])
			{
				continue;
			}

			temp = queries[i].split('=');
			params[temp[0]] = temp[1];
		}

		return params;
	};
})(jQuery);
