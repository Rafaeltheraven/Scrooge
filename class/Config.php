<?php

/**
 * Config handler which reads a given config file
 * and returns config values.
 * @author Rafael Dulfer
 */
class Config {

	private $config;

	/**
	 * Constructor for the class.
	 * 
	 * A path to a config file can be passed to the constructor. If no
	 * path is passed, then ../config/config.ini.php is taken as the default.
	 * 
	 * @param string $path Path to config file.
	 * 
	 * @return Config
	 */ 
	public function __construct($path='') {
		if ($path == '') {
			$path = __DIR__ . '/../config/config.ini.php';
		}
		$this->config = parse_ini_file($path);
	}

	/**
	 * Gets a specific config option.
	 * 
	 * Will return a specific config option. A default value can be passed
	 * which will be returned if the config option was not set. By default
	 * the default value is null.
	 * 
	 * @param string $field The config option to get
	 * @param mixed $default The default value for this option
	 * 
	 * @return mixed
	 */
	public function get(string $field, $default = null) {
		if (isset($this->config[$field])) {
			return $this->config[$field];
		} else {
			return $default;
		}
	}
}

?>
