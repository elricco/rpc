<?php

$mform = new MForm();
// link button
$mform->addLinkField(1,array('label'=>'Link zu den Optionen'));
$mform->addLinkField(2,array('label'=>'Link zur Zusammenfassung'));
// parse form
echo $mform->show();

?>