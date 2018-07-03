<?php
/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

/**
 * Uri Helper
 *
 * This class provides a UTF-8 safe version of parse_url().
 *
 * @since  1.0
 */
class UriHelper
{
	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string  $url  URL to parse
	 *
	 * @return  mixed  Associative array with url parts or false if badly formed URL.
	 *
	 * @see     https://secure.php.net/manual/function.parse-url.php
	 * @since   1.0
	 */
	public static function parse_url(string $url, int $component = -1)
	{
		$urlParts = parse_url($url, -1);

		// If no UTF-8 chars in the url or system is using a utf-8 locale just parse it using php native parse_url which is faster.
		if (utf8_decode($url) === $url || self::unparse_url($urlParts) === $url)
		{
			return $urlParts;
		}

		// URL with UTF-8 chars in the url.

		// Build the reserved uri encoded characters map.
		$reservedUriCharactersMap = [
			'%21' => '!',
			'%2A' => '*',
			'%27' => '\'',
			'%28' => '(',
			'%29' => ')',
			'%3B' => ';',
			'%3A' => ':',
			'%40' => '@',
			'%26' => '&',
			'%3D' => '=',
			'%24' => '$',
			'%2C' => ',',
			'%2F' => '/',
			'%3F' => '?',
			'%23' => '#',
			'%5B' => '[',
			'%5D' => ']',
		];

		// Encode the URL (so UTF-8 chars are encoded), revert the encoding in the reserved uri characters and parse the url.
		$parts = parse_url(strtr(urlencode($url), $reservedUriCharactersMap), $component);

		// With a well formed url decode the url (so UTF-8 chars are decoded).
		return $parts ? array_map('urldecode', $parts) : $parts;
	}

	/**
	 * Unparses a URL parsed by PHP parse_url function
	 *
	 * @param   array  The url parts.
	 *
	 * @return  string  $url  The URL unparsed
	 *
	 * @see     https://secure.php.net/manual/function.parse-url.php
	 * @since   2.0
	 */
	public static function unparse_url(array $parsedUrl = []): string
	{
		return (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '')
			. (isset($parsedUrl['user']) ? $parsedUrl['user'] . (isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '') . '@'  : '')
			. (isset($parsedUrl['host']) ? $parsedUrl['host'] : '')
			. (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '')
			. (isset($parsedUrl['path']) ? $parsedUrl['path'] : '')
			. (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '')
			. (isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '');
	}
}
