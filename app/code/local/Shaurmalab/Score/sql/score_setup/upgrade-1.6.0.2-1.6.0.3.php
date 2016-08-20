<?php
$installer = $this;

$installer->startSetup();

$installer->run("


ALTER TABLE  `score_oggetto_link` ADD  `position` INT NOT NULL;
");


$installer->endSetup();
