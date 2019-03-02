<?php

$mform = new MForm();
// link button
$mform->addLinkField(1,array('label'=>'Link zum Konfigurator'));
$mform->addLinkField(2,array('label'=>'Link zu den Lieferangaben'));
// parse form
echo $mform->show();

?>