<?php

class FileDirectory_File_Extension extends DataExtension
{
	private static $belongs_many_many = array(
		'FileDirectoryPages' => 'FileDirectoryPage'
	);
	
	public function SecureDownloadLink()
	{
		if (class_exists('ProtectedAreaPage') && class_exists('ProtectedAreaUser'))
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