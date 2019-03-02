<?php

$mform = new MForm();
// link button
$mform->addLinkField(1,array('label'=>'Link zu den Optionen'));
// parse form
echo $mform->show();

?>