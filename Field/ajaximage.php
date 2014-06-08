<?php
/**
 * Part of SMS Ajax Image.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use SMSAjaxImageBundle\AjaxImage;
use Windwalker\Helper\XmlHelper;

/**
 * Class JFormFieldAjaxImage
 *
 * XML properties:
 * - profile:       Config profile.
 * - previewWidth:
 * - previewHeight:
 * - uploadTask:
 * - deleteTask:
 *
 * @since 1.0
 */
class JFormFieldAjaximage extends \JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Ajaximage';

	/**
	 * Property initialised.
	 *
	 * @var  boolean
	 */
	protected static $initialised = false;

	/**
	 * getInput
	 *
	 * @return  string
	 */
	public function getInput()
	{
		$profile   = XmlHelper::get($this->element, 'profile', 'default');
		$ajaxImage = AjaxImage::getInstance($profile);
		$class     = XmlHelper::get($this->element, 'class');
		$fieldname = $this->fieldname;

		$option['previewWidth'] = XmlHelper::get($this->element, 'previewWidth', $ajaxImage->get('previewWidth', 75));
		$option['previewHeight'] = XmlHelper::get($this->element, 'previewHeight', $ajaxImage->get('previewHeight', 75));
		$option['textOnly'] = XmlHelper::getBool($this->element, 'text_only', false);

		$this->prepareScript($ajaxImage, $option);

		$previewDisplay = $this->value ? 'block' : 'none';
		$uploadDisplay = $this->value ? 'none' : 'block';

		$marginTop = ($option['previewHeight'] / 2) - 15 . 'px';

		$html = <<<HTML
	<!-- Display image -->
	<div class="ajaximage-wrap ajaximage-{$fieldname} profile-{$profile} {$class} well well-small">
		<div class="display-box" style="display: {$previewDisplay}">
			<!-- image -->
			<img class="display-image" src="" />

			<!-- image name -->
			<div class="display-name" style="margin-top: {$marginTop}">
				...
			</div>

			<!-- remove button -->
			<button class="remove-input btn" type="button" style="margin-top: {$marginTop};"><i class="icon-trash"></i></button>
		</div>

		<!-- Upload -->
		<div class="upload-box" class="row" style="display: {$uploadDisplay}">
			<button type="button" class="btn btn-info" onclick="jQuery(this).parent().find('.upload-input').click();">上傳</button>
			<input class="upload-input" name="image" type="file" style="display: none" />
		</div>

		<div class="progress-wrap" style="display: none">
			<div class="progress progress-striped active">
			  <div class="bar" style="width: 0%;"></div>
			</div>
		</div>

		<input class="image-path" id="{$this->id}" type="hidden" name="{$this->name}" value="{$this->value}" />
	</div>
HTML;

		return $html;
	}

	/**
	 * prepareScript
	 *
	 * @param   AjaxImage $ajaxImage
	 * @param   array     $option
	 *
	 * @return  void
	 */
	protected function prepareScript($ajaxImage, $option = array())
	{
		static::loadScript();

		$doc = \JFactory::getDocument();
		$profile = XmlHelper::get($this->element, 'profile', 'default');
		$handler = $ajaxImage->get('handler', 'com_flower');

		/** @var $ajaxImageOption \Joomla\Registry\Registry */
		$ajaxImageOption = $ajaxImage->getOption();

		$defaultOption = array(
			'rootUrl' => JUri::root(),
			'currentUrl' => (string) JUri::getInstance(),
			'profile' => $profile,
			'handler' => $handler,
			'uploadTask' => XmlHelper::get($this->element, 'uploadTask', $ajaxImage->get('upload.task', 'image.ajax.upload')),
			'deleteTask' => XmlHelper::get($this->element, 'deleteTask', $ajaxImage->get('delete.task', 'image.ajax.delete'))
		);

		$ajaxImageOption->loadArray($option)
			->loadArray($defaultOption);

		$doc->addScriptDeclaration(
			<<<JS
jQuery(document).ready(function()
{
	if (undefined == window.imageAjax)
	{
		window.imageAjax = [];
	}

	window.imageAjax['{$this->fieldname}'] = new ImageAjaxField('{$this->fieldname}', '{$profile}', {$ajaxImageOption});
});
JS
		);
	}

	/**
	 * loadScript
	 *
	 * @return  void
	 */
	protected static function loadScript()
	{
		if (static::$initialised)
		{
			return;
		}

		$doc = \JFactory::getDocument();

		JHtmlJquery::framework(true);

		$doc->addScriptVersion(AjaxImage::RESOURCE_URL . '/asset/js/URI.js');
		$doc->addScriptVersion(AjaxImage::RESOURCE_URL . '/asset/js/js-thumb.js');
		$doc->addScriptVersion(AjaxImage::RESOURCE_URL . '/asset/js/ajax-image.js');

		$doc->addStyleSheetVersion(AjaxImage::RESOURCE_URL . '/asset/css/ajax-image.css');

		static::$initialised = true;
	}
}
