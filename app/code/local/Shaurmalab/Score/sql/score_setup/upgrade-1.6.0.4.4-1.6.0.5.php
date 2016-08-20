<?php
$installer = $this;

$installer->startSetup();

$installer->run("
UPDATE dcontent_templates SET product = REPLACE(product,'span','col-md-');
UPDATE dcontent_templates SET product = REPLACE(product,'<col-md-','<span');
UPDATE cms_page SET content = REPLACE(content,'span','col-md-');
UPDATE cms_page SET content = REPLACE(content,'<col-md-','<span');
UPDATE cms_block SET content = REPLACE(content,'span','col-md-');
UPDATE cms_block SET content = REPLACE(content,'<col-md-','<span');
");

$installer->endSetup();