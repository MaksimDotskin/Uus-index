<?php
$password="Ltee";
$cool="superpaev";
$krypt=crypt($password, $cool);
echo $krypt;
echo date( "Y-m-d H:i:s");