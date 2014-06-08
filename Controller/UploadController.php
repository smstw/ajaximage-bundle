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
class UploadController extends DisplayController
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

		$file = $this->input->files->getVar('image');
		$ext  = \JArrayHelper::getValue($this->extMapper, $file['type']);

		if (empty($file['tmp_name']))
		{
			$json['msg'] = 'No upload file.';

			exit($json);
		}

		// Prepare folder
		$path = $ajaxImage->getUploadPath();
		$location = JPATH_ROOT . '/' . $path;

		if (!is_dir($location))
		{
			\JFolder::create($location);
		}

		// Build name
		$imageName = md5_file($file['tmp_name']) . '.' . $ext;
		$imageLocation = $location . '/' . $imageName;
		$imagePath = $path . '/' . $imageName;

		// Setting return data
		$json['path'] = str_replace(array('\\'), '/', $imagePath);
		$json['title'] = $imageName;

		// Start upload
		if (! \JFile::upload($file['tmp_name'], $imageLocation))
		{
			$json['msg'] = 'Move file fail.';

			exit($json);
		}

		$json['success'] = true;

		exit($json);
	}
}
 