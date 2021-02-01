<?php

namespace IQnection\FileDirectory\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use IQnection\FileDirectory\FileDirectoryPage;
use IQnection\ProtectedArea\Model\ProtectedAreaUser;

class FileDirectoryFileExtension extends DataExtension
{
	private static $belongs_many_many = [
		'FileDirectoryPages' => FileDirectoryPage::class
	];

	public function FullSourcePath()
	{
		return Director::baseFolder() . $this->owner->getSourceURL();
	}

	public function SecureDownloadLink()
	{
		if (class_exists('IQnection\\ProtectedArea\\ProtectedAreaPage') && class_exists('IQnection\\ProtectedArea\\Model\\ProtectedAreaUser'))
		{
			if ($User = ProtectedAreaUser::CurrentSiteUser())
			{
				$downloadHash = md5(md5($User->UserHash).md5($this->owner->ID).md5($this->owner->Created));
				$Controller = Controller::curr();
				return $Controller->Link('secure_download/'.$downloadHash);
			}
		}
		return $this->owner->getURL();
	}
}