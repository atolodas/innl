<?php
$installer = $this;

$installer->startSetup();

$installer->run("
	update score_eav_attribute set used_in_oggetto_listing = 1 where is_visible_on_front = 1  or is_public = 1;
");

$installer->endSetup();