<?php
$kasutaja='d123173_maksdot';
$serverinimi='d123173.mysql.zonevs.eu';
//$serverinimi='d123173.mysql.zonevs.eu';
$parool='Tark123456';
$andmebaas='d123173_index2';
$yhendus=NEW mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus->set_charset('UTF8');

$yhendus2=NEW mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus2->set_charset('UTF8');