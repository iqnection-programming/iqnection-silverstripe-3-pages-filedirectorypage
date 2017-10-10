<?php

class FileDirectoryPage extends Page
{
	private static $icon = 'iq-filedirectorypage/images/file-directory-page.png';
	private static $upload_directory = 'directory-files';
	
	private static $has_one = array(
		'CustomIcon' => 'Image'
	);
	
	private static $many_many = array(
		'Files' => 'File'
	);
	
	private static $allowed_children = array(
		'FileDirectoryPage',
		'Page'
	);
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', UploadField::create('CustomIcon','Custom Icon')
			->setAllowedExtensions(array('jpg','jpeg','png','gif')) );
		$uploadDirectory = Folder::find_or_make($this->Config()->get('upload_directory'));
		$fields->addFieldToTab('Root.Files', HeaderField::create('filesnote','These files will be listed when this page is viewed. You may select existing files that are displayed on another page as well. Enter the file name in the search field, select the file, and click "Link Existing"',4) );
		$fields->addFieldToTab('Root.Files', GridField::create(
			'Files',
			'Files',
			$this->Files(),
			$gfConfig = GridFieldConfig_RelationEditor::create()
		));
		$gfConfig->removeComponentsByType('GridFieldAddNewButton');
		$gfConfig->getComponentByType('GridFieldAddExistingAutocompleter')
			->setSearchList(File::get()->filter('ParentID',$uploadDirectory->ID));

		$fields->addFieldToTab('Root.UploadFiles', HeaderField::create('uploadheader','Upload files and Save page to link',2) );
		$fields->addFieldToTab('Root.UploadFiles', UploadField::create('AttachedFiles','Upload Files')
			->setFolderName($uploadDirectory->Name)
			->setCanAttachExisting(false) );
		return $fields;
	}
	
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();
		if ( (isset($_REQUEST['AttachedFiles']['Files'])) && (is_array($_REQUEST['AttachedFiles']['Files'])) )
		{
			foreach($_REQUEST['AttachedFiles']['Files'] as $fileID)
			{
				$this->Files()->add($fileID);
			}
		}
	}
}

class FileDirectoryPage_Controller extends Page_Controller
{
	private static $allowed_actions = array(
		'secure_download'
	);
	
	public function PagedFiles()
	{
		$list = PaginatedList::create($this->Files(),$this->getRequest());
		$list->setPageLength(36);
		return $list;
	}
	
	public function secure_download()
	{
		//$downloadHash = md5(md5($User->UserHash).md5($this->owner->ID).md5($this->owner->Created));
		$downloadHash = $this->request->param('ID');
		if ($User = ProtectedAreaUser::CurrentSiteUser())
		{
			// find the file
			if ($File = File::get()->where("MD5(MD5(CONCAT(MD5('".$User->UserHash."'),MD5(ID),MD5(Created)))) = MD5('".$downloadHash."')")->First())
			{
				$fileData = file_get_contents($File->getFullPath());
				return SS_HTTPRequest::send_file($fileData,$File->Name);
			}
			return $this->Customise(array('Content' => 'Could not find the file'))->renderWith('Page');
		}
		return $this->httpError(404);
	}
}












