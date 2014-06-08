<?php
/**
 * Part of SMS Ajax Image.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace SMSAjaxImageBundle;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * Class AjaxImageProvider
 *
 * @since 1.0
 */
class AjaxImageProvider implements ServiceProviderInterface
{
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
	 * Class init.
	 *
	 * @param string          $profile
	 * @param array|Registry  $option
	 */
	public function __construct($profile, $option = array())
	{
		$this->profile = $profile;

		$this->option = ($option instanceof Registry) ? $option : new Registry($option);
	}

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 */
	public function register(Container $container)
	{
		$resolver = $container->get('controller.resolver');

		$resolver->registerTask(
			$this->option->get('upload.task', 'image.ajax.upload'),
			$this->option->get('upload.controller', '\\SMSAjaxImageBundle\\Controller\\UploadController')
		);

		$resolver->registerTask(
			$this->option->get('delete.task', 'image.ajax.delete'),
			$this->option->get('delete.controller', '\\SMSAjaxImageBundle\\Controller\\DeleteController')
		);

		$container->share(
			'ajax.image.' . $this->profile,
			AjaxImage::getInstance($this->profile, $this->option)
		);
	}
}
