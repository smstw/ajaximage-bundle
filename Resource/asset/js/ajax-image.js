/**
 * Part of SMS Ajax Image.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

;(function($, undefined)
{
	"use strict";

	// Prevent global conflict.
	if (window.ImageAjaxField !== undefined)
	{
		return;
	}

	/**
	 * Class ImageAjaxField
	 *
	 * @param  {string}  name     Field name
	 * @param  {string}  profile  Image uri proxy
	 * @param  {Object}  option   Ajax post uri
	 *
	 * @constructor
	 */
	window.ImageAjaxField = function(name, profile, option)
	{
		/**
		 * Main container.
		 *
		 * @type {HTMLElement}
		 */
		this.fieldContainer = $('.ajaximage-' + name);

		/**
		 * Upload file input
		 *
		 * @type {HTMLElement}
		 */
		this.uploadInput = this.fieldContainer.find("input.upload-input");

		/**
		 * Remove image button
		 *
		 * @type {HTMLElement}
		 */
		this.removeImage = this.fieldContainer.find(".remove-input");

		/**
		 * Display image dataset block
		 *
		 * @type {HTMLElement}
		 */
		this.displayBox = this.fieldContainer.find(".display-box");

		/**
		 * Upload image input block
		 *
		 * @type {HTMLElement}
		 */
		this.uploadBox = this.fieldContainer.find(".upload-box");

		/**
		 * Display image
		 *
		 * @type {HTMLElement}
		 */
		this.displayImage = this.fieldContainer.find(".display-image");

		/**
		 * Display image name
		 *
		 * @type {HTMLElement}
		 */
		this.displayName = this.fieldContainer.find(".display-name");

		/**
		 * Image database id
		 *
		 * @type {HTMLElement}
		 */
		this.imagePath = this.fieldContainer.find(".image-path");

		/**
		 * Progress bar wrap.
		 *
		 * @type {HTMLElement}
		 */
		this.progressBar = this.fieldContainer.find('.progress-wrap');

		/**
		 * Progress.
		 *
		 * @type {HTMLElement}
		 */
		this.progress = this.fieldContainer.find('.progress .bar');

		/**
		 * Options.
		 *
		 * @type {Object}
		 */
		this.option = option;

		// 初始化 HTMLElement event
		this.registerEvents();
	};

	ImageAjaxField.prototype = {
		/**
		 * Register events.
		 *
		 * @return  void
		 */
		registerEvents: function()
		{
			var field = this;

			this.uploadInput.change(function()
			{
				field.uploadImage();
			});

			this.removeImage.click(function(event)
			{
				if (confirm("確定要刪除嗎?"))
				{
					field.deleteImage();
				}
			});
		},

		/**
		 * 如果有對應圖片時刷新 field 顯示
		 *
		 * @param   {string}  title 圖片名稱
		 * @param   {string}  path  圖片位置
		 *
		 * @return  void
		 */
		flushImage: function(title, path)
		{
			var self = this;

			this.displayBox.show();
			this.uploadBox.hide();

			var image = new Image();

			// Prepare an onload event to resize image.
			image.onload = function()
			{
				var thumb = jsthumb.resize(image, {
					maxWidth: self.option.previewWidth || 75,
					maxHeight: self.option.previewHeight || 75
				});

				self.displayImage.attr('src', thumb);
			};

			image.src = this.option.rootUrl + "/" + path;

			this.displayName.text(title);
		},

		/**
		 * 上傳圖片
		 *
		 * @return  void
		 */
		uploadImage: function()
		{
			var field = this;
			var post  = new FormData();
			var files = this.uploadInput.prop("files");
			var uri   = new URI(this.option.rootUrl);

			uri.setQuery({
				option: this.option.handler,
				task: this.option.uploadTask || 'image.ajax.upload',
				profile: this.option.profile || 'default'
			});

			post.append("image", files[0]);
			post.append("task", "image.ajax.upload");

			$.ajax({
				url: uri.toString(),
				type: "post",
				dataType: "json",
				data: post,
				processData: false,
				contentType: false,
				xhrFields:
				{
					// add listener to XMLHTTPRequest object directly for progress (jquery doesn't have this yet)
					onprogress: function (progress)
					{
						field.progressBar.show();
						field.uploadInput.hide();

						// calculate upload progress
						var percentage = Math.floor((progress.total / progress.totalSize) * 100);

						// log upload progress to console
						field.progress.css('width', percentage + '%');

						if (percentage === 100)
						{
							field.progressBar.hide();
						}
					}
				},
				success: function(data)
				{
					if (data.success)
					{
						field.imagePath.val(data.path);
						field.uploadInput.val('');

						field.flushImage(data.title, data.path);
					}
					else
					{
						alert(data.msg);
					}
				}
			});
		},

		/**
		 * 刪除圖片
		 *
		 * @return  void
		 */
		deleteImage: function()
		{
			var field = this;
			var uri   = new URI(this.option.rootUrl);

			uri.setQuery({
				option: this.option.handler,
				task: this.option.deleteTask || 'image.ajax.delete',
				profile: this.option.profile || 'default'
			});

			$.ajax({
				url: uri.toString(),
				type: "post",
				dataType: "json",
				data: {
					path: this.imagePath.val()
				},
				success: function(data)
				{
					if (!data.success)
					{
						alert(data.msg);

						return;
					}

					// Show file input
					field.displayBox.hide();
					field.uploadBox.show();

					// Clear value
					field.imagePath.val("");
				}
			});
		}
	};

	window.ImageAjaxField = ImageAjaxField;
})(jQuery);
