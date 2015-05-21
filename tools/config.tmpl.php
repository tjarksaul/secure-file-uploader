<?php

session_start();

// dir in which the uploaded files will be saved (must be writable by the web server)
define('UL_DIR', 'upload/');

// file which is used as file dictionary (must be writable by the web server)
define('FILE_DATABASE', 'upload/files.json');

// format of the url to output. %1$s is the file name, %2$s is the security hash
define('URL_FORMAT', 'https://example.com/files/%2$s/%1$s'); 

// PHPASS-Hash of the login password
define('PASSWORD_HASH', '*');
