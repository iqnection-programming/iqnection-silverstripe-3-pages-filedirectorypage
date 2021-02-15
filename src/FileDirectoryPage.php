<?php

namespace IQnection\FileDirectory;

use SilverStripe\Forms;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Folder;
use SilverStripe\Core\Injector\Injector;

class FileDirectoryPage extends \Page
{
	private static $table_name = 'FileDirectoryPage';
	private static $icon = 'iqnection-pages/filedirectorypage:client/images/file-directory-page.png';
	private static $upload_directory = 'directory-files';

	private static $has_one = [
		'CustomIcon' => Image::class
	];

	private static $many_many = [
		'Files' => File::class
	];

	private static $owns = [
		'Files'
	];

	private static $allowed_children = [
		'FileDirectoryPage',
		'Page'
	];

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', Injector::inst()->create(Forms\FileHandleField::class, 'CustomIcon')
			->setAllowedExtensions(array('jpg','jpeg','png','gif')) );
		$uploadDirectory = Folder::find_or_make($this->Config()->get('upload_directory'));
		$fields->addFieldToTab('Root.Files', Forms\HeaderField::create('filesnote','These files will be listed when this page is viewed. You may select existing files that are displayed on another page as well. Enter the file name in the search field, select the file, and click "Link Existing"',4) );
		$fields->addFieldToTab('Root.Files', Forms\GridField\GridField::create(
			'Files',
			'Files',
			$this->Files(),
			$gfConfig = Forms\GridField\GridFieldConfig_RelationEditor::create()
		));
		$gfConfig->removeComponentsByType(Forms\GridField\GridFieldAddNewButton::class);
		$gfConfig->getComponentByType(Forms\GridField\GridFieldAddExistingAutocompleter::class)
			->setSearchList(File::get()->filter('ParentID',$uploadDirectory->ID));

		$fields->addFieldToTab('Root.UploadFiles', Forms\HeaderField::create('uploadheader','Upload files and Save page to link',2) );
		$fields->addFieldToTab('Root.UploadFiles', Injector::inst()->create(Forms\FileHandleField::class, 'AttachedFiles','Upload Files')
			->setFolderName($uploadDirectory->Name) );
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













