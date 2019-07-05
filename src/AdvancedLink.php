<?php

namespace WebTorque\AdvancedLink;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Requirements;

class AdvancedLink extends DataObject
{
    private static $table_name = 'AdvancedLink';
    private static $singular_name = 'Link';
    private static $plural_name = 'Links';

    private static $db = [
        'LinkType' => 'Varchar',
        'Link' => 'Text',
        'TargetBlank' => 'Boolean',
        'CTAText' => 'Varchar(100)',
        'Parameter' => 'Varchar',
        'PopUpID' => 'Varchar'
    ];

    private static $allowed_types = [
        'Internal' => 'Internal',
        'External' => 'External',
        'File' => 'File',
        'Phone' => 'Phone',
        'Email' => 'Email',
        'Popup' => 'Popup'
    ];

    private static $defaults = [
        'LinkType' => 'Internal'
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
        $fields = new FieldList(new TabSet("Root", $tabMain = new Tab('Main',
            OptionsetField::create('LinkType', 'Type', Config::inst()->get(AdvancedLink::class, 'allowed_types')),
            LiteralField::create('Tip', '<p class="message warning">Please save the link type to see options change.</p>')
        )));

        if ($this->LinkType == 'Internal') {
            $fields->addFieldsToTab('Root.Main', [
                TreeDropdownField::create('PageID', 'Page', SiteTree::class),
                TextField::create('Parameter', 'Extra Parameter'),
                TextField::create('CTAText', 'CTA Text'),
                CheckboxField::create('TargetBlank', 'Open in new tab?'),
            ]);
        } elseif ($this->LinkType == 'External') {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Link', 'External Link'),
                TextField::create('Parameter', 'Extra Parameter'),
                TextField::create('CTAText', 'CTA Text'),
                CheckboxField::create('TargetBlank', 'Open in new tab?'),
            ]);
        } elseif ($this->LinkType == 'File') {
            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('File', 'File'),
                TextField::create('CTAText', 'CTA Text'),
            ]);
        } elseif ($this->LinkType == 'Popup') {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('PopupID', 'Popup container ID'),
                TextField::create('CTAText', 'CTA Text'),
            ]);
        } elseif ($this->LinkType == 'Email' || $this->LinkType == 'Phone') {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Link', 'Phone or Email')->setDescription('The link will open with the default application for handling Email and Phone.'),
                TextField::create('CTAText', 'CTA Text'),
            ]);
        }

        $this->extend('updateCMSFields', $fields);

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
                $link = (strpos($this->Link, "http://") === false && strpos($this->Link, "https://") === false) ? 'https://' . $this->Link : $this->Link;
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

        $this->extend('updateURL', $link);

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

        if (!$newTab || $newTab == 0) {
            $newTab = false;
        }

        $this->extend('updateNewTab', $newTab);

        return $newTab;
    }
}