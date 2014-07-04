<?php

	class NexttLoader {
		
		/**
		 * @var array
		 */
		private static $__classPath = array();

		public static function import($path) {
			$path = str_replace('.','/',$path);
			if (is_dir($path) && is_readable($path)) {
				$path = trim($path);
				if (!in_array($path,self::$__classPath)) {
					self::$__classPath[] = $path;
				}
			}
		}

		public static function loadClass ($class) {

			if (class_exists($class)) return;

			foreach (self::$__classPath as $dir ) {
			
				$path = str_replace('\\', '/', substr($class, 1));
				$path = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $class;

				if (is_file($path . '.php')) {
					require $path . '.php';
					break;
				}
			}
		}
	}
	
	if (empty(NexttLoader::$__classPath)) {
		NexttLoader::import(dirname(__FILE__) . DIRECTORY_SEPARATOR .'..');
	}
	spl_autoload_register(array('NexttLoader', 'loadClass'));
