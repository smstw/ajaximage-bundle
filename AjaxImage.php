<?php
/**
 * Part of SMS Ajax Image.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace SMSAjaxImageBundle;

use Joomla\Registry\Registry;
use Windwalker\Helper\DateHelper;

define('SMS_AJAX_IMAGE_PATH',          __DIR__);
define('SMS_AJAX_IMAGE_PATH_FIELD',    SMS_AJAX_IMAGE_PATH . '/Field');
define('SMS_AJAX_IMAGE_PATH_RESOURCE', SMS_AJAX_IMAGE_PATH . '/Resource');

$url = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', SMS_AJAX_IMAGE_PATH_RESOURCE);

define('SMS_AJAX_IMAGE_URL_RESOURCE', str_replace('\\', '/', $url));

/**
 * Class AjaxImage
 *
 * @since 1.0
 */
class AjaxImage
{
	/**
	 * @const string
	 */
	const PATH = SMS_AJAX_IMAGE_PATH;

	/**
	 * @const string
	 */
	const FIELD_PATH = SMS_AJAX_IMAGE_PATH_FIELD;

	/**
	 * @const string
	 */
	const RESOURCE_PATH = SMS_AJAX_IMAGE_PATH_RESOURCE;

	/**
	 * @const string
	 */
	const RESOURCE_URL = SMS_AJAX_IMAGE_URL_RESOURCE;

	/**
	 * Property instances.
	 *
	 * @var  array
	 */
	private static $instances = array();

	/**
	 * Property initialised.
	 *
	 * @var  boolean
	 */
	private static $initialised = false;

	/**
	 * Property profile.
	 *
	 * @var  string
	 */
	protected $profile = null;

	/**
	 * Property option.
	 *
	 * @var  Registry
	 */
	protected $option = null;

	/**
	 * getInstance
	 *
	 * @param string   $profile
	 * @param Registry $option
	 *
	 * @return  mixed
	 */
	public static function getInstance($profile, Registry $option = null)
	{
		if (empty(self::$instances[$profile]))
		{
			self::$instances[$profile] = new static($profile, $option);
		}

		return self::$instances[$profile];
	}

	/**
	 * Class init.
	 *
	 * @param string   $profile
	 * @param Registry $option
	 */
	protected function __construct($profile, Registry $option = null)
	{
		static::prepareEnvironment();

		$this->option = $option ? : new Registry($option);
		$this->profile = $profile;
	}

	/**
	 * get
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		return $this->option->get($key, $default);
	}

	/**
	 * set
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return  $this
	 */
	public function set($key, $value)
	{
		$this->option->set($key, $value);

		return $this;
	}

	/**
	 * prepareEnvironment
	 *
	 * @return  void
	 */
	public static function prepareEnvironment()
	{
		if (self::$initialised)
		{
			return;
		}

		\JForm::addFieldPath(self::FIELD_PATH);

		self::$initialised = true;
	}

	/**
	 * getUploadPath
	 *
	 * @return  string
	 */
	public function getUploadPath()
	{
		$basePath = $this->get('basePath', 'tmp');
		$uploadFolder = $this->get('uploadFolder');

		$path = $basePath . DIRECTORY_SEPARATOR . $uploadFolder;

		$date = DateHelper::getDate();
		$user = \JFactory::getUser();
		$session = \JFactory::getSession();

		$replace = array(
			':year' => $date->year,
			':month' => $date->month,
			':day' => $date->day,
			':user_id' => $user->id,
			':username' => $user->username,
			':user' => $user->name,
			':session' => $session->getId()
		);

		return \JPath::clean(strtr($path, $replace));
	}

	/**
	 * removeExpiredFolders
	 *
	 * @return  void
	 */
	public function removeExpiredFolders()
	{
		$day = $this->get('cleanPeriod', 2);

		if (!$day)
		{
			return;
		}

		$basePath = $this->get('basePath', 'tmp');

		$files = new \FilesystemIterator(JPATH_ROOT . '/' . $basePath);

		foreach ($files as $file)
		{
			if ($file->isDot())
			{
				continue;
			}

			$filelastmodified = filemtime($file);

			if ((time() - $filelastmodified) > $day * 24 * 3600)
			{
				unlink($file);
			}
		}
	}

	/**
	 * getOption
	 *
	 * @return  \Joomla\Registry\Registry
	 */
	public function getOption()
	{
		return $this->option;
	}

	/**
	 * setOption
	 *
	 * @param   \Joomla\Registry\Registry $option
	 *
	 * @return  AjaxImage  Return self to support chaining.
	 */
	public function setOption($option)
	{
		$this->option = $option;

		return $this;
	}

	/**
	 * getProfile
	 *
	 * @return  string
	 */
	public function getProfile()
	{
		return $this->profile;
	}

	/**
	 * setProfile
	 *
	 * @param   string $profile
	 *
	 * @return  AjaxImage  Return self to support chaining.
	 */
	public function setProfile($profile)
	{
		$this->profile = $profile;

		return $this;
	}
}
