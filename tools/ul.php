<?php

require_once 'config.php';

header('Content-type: application/json');

if (!$_SESSION['login']) { exit; }

if (!isset($_FILES['file'])) { die(json_encode(['code' => -2, 'message' => 'You did not send a file'])); }

$fileName = basename($_FILES['file']['name']);
$fileName = convert_ascii($fileName);

// if the file exists, we fix the name
// TODO: limit the iterations
while (file_exists(UL_DIR . $fileName)) {
    $pathInfo = pathinfo($fileName);
    $fileName = $pathInfo['filename'] . '_.' . $pathInfo['extension'];
}

// now we got a unique name and can save the file
if (move_uploaded_file($_FILES['file']['tmp_name'], UL_DIR . $fileName)) {
    // generating the identifying hash for the file
    $rand = openssl_random_pseudo_bytes(32);
    $hasher = hash_init('sha256');
    hash_update_file($hasher, UL_DIR . $fileName);
    hash_update($hasher, $rand);
    $hash = hash_final($hasher);

    // saving the hash-file-relation in our json database
    $json = file_get_contents(FILE_DATABASE);
    $dict = json_decode($json, true);
    $dict[$fileName] = $hash;
    $json = json_encode($dict);
    file_put_contents(FILE_DATABASE, $json);

    // finally, we generate the URL and return it
    print json_encode(['code' => 0, 'message' => 'Success', 'url' => sprintf(URL_FORMAT, $fileName, $hash)]);
} else {
    print json_encode(['code' => -3, 'message' => 'Sorry, couldn\'t save file']);
}


function convert_ascii($string) { 
    // replace spaces 
    $search[] = " ";
    $replace[] =  "-";

    // Replace Single Curly Quotes
    $search[]  = chr(226).chr(128).chr(152);
    $replace[] = "'";
    $search[]  = chr(226).chr(128).chr(153);
    $replace[] = "'";

    // Replace Smart Double Curly Quotes
    $search[]  = chr(226).chr(128).chr(156);
    $replace[] = '"';
    $search[]  = chr(226).chr(128).chr(157);
    $replace[] = '"';

    // Replace En Dash
    $search[]  = chr(226).chr(128).chr(147);
    $replace[] = '--';

    // Replace Em Dash
    $search[]  = chr(226).chr(128).chr(148);
    $replace[] = '---';

    // Replace Bullet
    $search[]  = chr(226).chr(128).chr(162);
    $replace[] = '*';

    // Replace Middle Dot
    $search[]  = chr(194).chr(183);
    $replace[] = '*';

    // Replace Ellipsis with three consecutive dots
    $search[]  = chr(226).chr(128).chr(166);
    $replace[] = '...';

    // Apply Replacements
    $string = str_replace($search, $replace, $string);

    // Remove any non-ASCII Characters
    $string = preg_replace("/[^\x01-\x7F]/","", $string);

    return $string; 
}
