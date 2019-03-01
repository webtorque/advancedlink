<?php

namespace WebTorque\AdvancedLink;

use SilverShop\HasOneField\GridFieldHasOneButtonRow;
use SilverStripe\Dev\Debug;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\GridField\GridFieldButtonRow;

class WTGridFieldHasOneButtonRow extends GridFieldHasOneButtonRow
{

    protected $record;

    public function __construct($record,$targetFragment = 'before')
    {
        $this->targetFragment = $targetFragment;
        $this->record = $record;
    }

    public function getHTMLFragments($gridField)
    {
        $data = new ArrayData(array(
            "GridField" => $gridField,
            "TargetFragmentName" => $this->targetFragment,
            "LeftFragment" => "\$DefineFragment(buttons-{$this->targetFragment}-left)",
            "RightFragment" => "\$DefineFragment(buttons-{$this->targetFragment}-right)",
            "Record" => $this->record
        ));

        $templates = SSViewer::get_templates_by_class($this, '', __CLASS__);
        return array(
            $this->targetFragment => $data->renderWith($templates)
        );
    }
}