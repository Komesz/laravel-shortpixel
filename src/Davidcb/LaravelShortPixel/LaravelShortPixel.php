<?php

namespace Davidcb\LaravelShortPixel;

use ShortPixel;

class LaravelShortPixel {

	protected $file;

	public function __construct()
	{
		ShortPixel\setKey(config('shortpixel.api_key'));
	}

	public function fromUrls($url, $path = null, $filename = null, $level = null, $width = null, $height = null, $max = false)
	{
		if (!$path) {
			$path = config('shortpixel.default_path');
		}

		$this->file = ShortPixel\fromUrls($url);
		$ret = $this->save($path, $filename, $level, $width, $height, $max);
	}

	public function fromFiles($url, $path = null, $level = null, $width = null, $height = null, $max = false)
	{
		if (!$path) {
			$path = config('shortpixel.default_path');
		}

		if (is_array($url)) {
			$this->file = ShortPixel\fromFiles($url);
		} else {
			$this->file = ShortPixel\fromFiles($url);
		}

		$ret = $this->save($path, null, $level, $width, $height, $max);
	}

	public function fromFolder($folder, $path = null, $level = null, $width = null, $height = null, $max = false)
	{
		\ShortPixel\ShortPixel::setOptions(array("persist_type" => "text"));

		if (!$path) {
			$path = config('shortpixel.default_path');
		}

		$stop = false;
		while (!$stop) {
			$this->file = ShortPixel\fromFolder($folder)->wait(300);
			$ret = $this->save($path, null, $level, $width, $height, $max);
			if (count($ret->succeeded) + count($ret->failed) + count($ret->same) + count($ret->pending) == 0) {
				$stop = true;
			}
		}
	}

	private function optimize($level = null)
	{
		if (!$level) {
			$level = config('shortpixel.compression_level');
		}

		return $this->file->optimize($level);
	}

	private function resize($width, $height, $max = false)
	{
		return $this->file->resize($width, $height, $max);
	}

	private function save($path, $filename = null, $level = null, $width = null, $height = null, $max = false)
	{
		$this->optimize($level);

		if ($width && $height) {
			$this->resize($width, $height, $max);
		}
		
		return $this->file->toFiles($path, $filename);
	}

}