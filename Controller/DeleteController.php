<?php
/**
 * Part of ihealth project. 
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace SMSAjaxImageBundle\Controller;

use Joomla\Registry\Registry;
use SMSAjaxImageBundle\AjaxImage;
use Windwalker\Controller\DisplayController;

/**
 * Class UploadController
 *
 * @since 1.0
 */
class DeleteController extends DisplayController
{
	/**
	 * Property extMapper.
	 *
	 * @var  array
	 */
	protected $extMapper = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png'
	);

	/**
	 * doExecute
	 *
	 * @return  mixed|void
	 */
	protected function doExecute()
	{
		$ajaxImage = AjaxImage::getInstance($this->input->get('profile', 'default'));
		$json = new Registry;

		$json['success'] = false;

		$path = $this->input->getString('path');

		$path = JPATH_ROOT . '/' . $path;

		if (is_file($path))
		{
			\JFile::delete($path);
		}

		$json['success'] = true;

		exit($json);
	}
}
