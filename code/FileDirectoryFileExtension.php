<?php

use SilverStripe\ORM;
use SilverStripe\Control;

class FileDirectoryFileExtension extends ORM\DataExtension
{
	private static $belongs_many_many = [
		'FileDirectoryPages' => FileDirectoryPage::class
	];
	
	public function FullSourcePath()
	{
		return Control\Director::baseFolder() . $this->owner->getSourceURL();
	}
	
	public function SecureDownloadLink()
	{
		if (class_exists('ProtectedAreaPage') && class_exists('ProtectedAreaUser'))
		{
			if ($User = ProtectedAreaUser::CurrentSiteUser())
			{
				$downloadHash = md5(md5($User->UserHash).md5($this->owner->ID).md5($this->owner->Created));
				$Controller = Control\Controller::curr();
				return $Controller->Link('secure_download/'.$downloadHash);
			}
		}
		return $this->owner->getURL();
	}
}