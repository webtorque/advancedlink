<?php

namespace WebTorque\AdvancedLink;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;

class AdvancedLink extends DataObject
{
    private static $table_name = 'AdvancedLink';
    private static $singular_name = 'Link';
    private static $plural_name = 'Links';

    private static $db = [
        'LinkType' => 'Enum("Internal,External,File,Phone,Email","Internal")',
        'Link' => 'Text',
        'TargetBlank' => 'Boolean',
        'CTAText' => 'Varchar(100)'
    ];

    private static $has_one = [
        'Page' => SiteTree::class,
        'File' => File::class
    ];

    private static $owns = [
        'File'
    ];

    private static $summary_fields = [
        'CTAText' => 'CTAText'
    ];

    private static $searchable_fields = [
        'CTAText'
    ];

    public function getCMSFields()
    {
        Requirements::javascript('app/advancedlink/js/AdvancedLink.js');

        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('CTAText', 'CTA Text'),
            CheckboxField::create('TargetBlank', 'Open in new tab?'),
            DropdownField::create('PageID', 'Page', SiteTree::get()->map('ID', 'Title')),
            OptionsetField::create('LinkType', 'Type', $this->dbObject('LinkType')->enumValues()),
            TextField::create('Link', 'External Link')->setDescription('If type is phone or email, the link will open with the default application for handling those. eg Skype or Outlook depending on the settings in your OS.'),
            UploadField::create('File', 'File')
        ));

        return $fields;
    }

    public function URL()
    {
        $link = '#';
        if ($this->LinkType == 'Internal') {
            if ($this->PageID > 0) {
                $link = $this->Page()->Link();
            }
        } elseif ($this->LinkType == 'File') {
            if ($this->FileID != '') {
                $link = $this->File()->Link();
            }
        } elseif ($this->Link != '') {
            if ($this->LinkType == 'External') {
                $link = (strpos($this->Link, "http://") === false) ? 'http://' . $this->Link : $this->Link;
            } elseif ($this->LinkType == 'Phone') {
                $link = 'tel:' . $this->Link;
            } elseif ($this->LinkType == 'Email') {
                $link = 'mailto:' . $this->Link;
            } elseif ($this->LinkType == 'Popup') {
                $link = '#';
            } else {
                $link = $this->Link;
            }
        }

        return $link;
    }

    public function NewTab()
    {
        $newTab = $this->TargetBlank;
        if ($this->LinkType == 'File') {
            $newTab = true;
        } elseif ($this->LinkType == 'Phone') {
            $newTab = false;
        } elseif ($this->LinkType == 'Email') {
            $newTab = false;
        }

        return $newTab;
    }
}