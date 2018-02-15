<?php

use SilverStripe\Assets\File;
use SilverStripe\Control;
use SilverStripe\ORM;

class FileDirectoryPageController extends PageController
{
	private static $allowed_actions = [
		'secure_download'
	];
	
	public function PagedFiles()
	{
		$list = ORM\PaginatedList::create($this->Files(),$this->getRequest());
		$list->setPageLength(36);
		return $list;
	}
	
	public function secure_download()
	{
		//$downloadHash = md5(md5($User->UserHash).md5($this->owner->ID).md5($this->owner->Created));
		$downloadHash = $this->request->param('ID');
		if ( (class_exists('ProtectedAreaUser')) && ($User = ProtectedAreaUser::CurrentSiteUser()) )
		{
			// find the file
			if ($File = File::get()->where("MD5(MD5(CONCAT(MD5('".$User->UserHash."'),MD5(ID),MD5(Created)))) = MD5('".$downloadHash."')")->First())
			{
				$fileData = file_get_contents($File->FullSourcePath());
				return Control\HTTPRequest::send_file($fileData,$File->Name);
			}
			return $this->Customise(array('Content' => 'Could not find the file'))->renderWith('Page');
		}
		return $this->httpError(404);
	}
}












