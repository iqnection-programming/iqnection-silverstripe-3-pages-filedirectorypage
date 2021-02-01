<?php

namespace IQnection\FileDirectory;

use SilverStripe\Assets\File;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\PaginatedList;
use IQnection\ProtectedArea\Model\ProtectedAreaUser;
use SilverStripe\View\Requirements;

class FileDirectoryPageController extends \PageController
{
	private static $allowed_actions = [
		'secure_download'
	];

	public function init()
	{
		parent::init();
		Requirements::css('iqnection-pages/filedirectorypage:client/css/FileDirectoryPage.css');
	}

	public function PagedFiles()
	{
		$list = PaginatedList::create($this->Files(),$this->getRequest());
		$list->setPageLength(36);
		return $list;
	}

	public function secure_download()
	{
		$downloadHash = $this->request->param('ID');
		if ( (class_exists('IQnection\\ProtectedArea\\Model\\ProtectedAreaUser')) && ($User = ProtectedAreaUser::CurrentSiteUser()) )
		{
			// find the file
			if ($File = File::get()->where("MD5(MD5(CONCAT(MD5('".$User->UserHash."'),MD5(ID),MD5(Created)))) = MD5('".$downloadHash."')")->First())
			{
				$fileData = file_get_contents($File->FullSourcePath());
				return HTTPRequest::send_file($fileData,$File->Name);
			}
			return $this->Customise(array('Content' => 'Could not find the file'))->renderWith('Page');
		}
		return $this->httpError(404);
	}
}












