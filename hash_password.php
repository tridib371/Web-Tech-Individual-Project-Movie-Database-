<?php
$password = 'admin1234'; // change to your desired admin password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
