<?php

/**
 * Mini - a micro PHP 5 framework
 *
 * @author tooreht <tooreht@gmail.com>
 * @copyright 2014 tooreht
 * @link http://www.miniframework.com
 * @license http://www.miniframework.com/license
 * @version 0.0.1
 * @package Mini
 */

namespace mini;

class AutoLoader
{
	/**
	* Mini PSR-0 autoloader
	*/
	public static function autoload($className)
	{
		// $thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);
		// $baseDir = __DIR__;

		// echo $className;
		// echo $thisClass;
		// echo __NAMESPACE__;

		$root = dirname(dirname(__FILE__));

		$fileName = $root . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

		// if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
		// $baseDir = substr($baseDir, 0, -strlen($thisClass));
		// }
		// $className = ltrim($className, '\\');
		// $fileName = $baseDir;
		// $namespace = '';
		// if ($lastNsPos = strripos($className, '\\')) {
		// $namespace = substr($className, 0, $lastNsPos);
		// $className = substr($className, $lastNsPos + 1);
		// $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		// }
		// $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		if (file_exists($fileName)) {
			require_once $fileName;
		}
	}
	/**
	* Register Mini's PSR-0 autoloader
	*/
	public static function register()
	{
		spl_autoload_register(__NAMESPACE__ . "\\AutoLoader::autoload");
	}
}
