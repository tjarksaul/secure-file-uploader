<?php
include 'tools/config.php';

$filename = !empty($_GET['file']) ? trim($_GET['file']) : '';
$hash = !empty($_GET['hash']) ? trim($_GET['hash']) : '';

$json = file_get_contents(FILE_DATABASE);
$dict = json_decode($json, true);

if (isset($dict[$filename])) { // the file exists (in the dictionary)
    if ($dict[$filename] === $hash) { // the given hash equals the saved file hash
        $file = UL_DIR . $filename;
        if (file_exists($file)) { // the file exists (on disk)
            // Wir finden erst mal den Mime-Type heraus
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);

            // Jetzt geben wir die Datei aus
            $fp = fopen($file, 'rb');

            // send the right headers
            header("Content-Type: $mime");
            header("Content-Length: " . filesize($file));

            // setting the file name
            header('Content-Disposition: inline; filename='.basename($filename));

            // dump the file and stop the script
            fpassthru($fp);
            fclose($fp);
            exit;
        }
    }
}

e404();

function e404() {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    $r = <<<EOD
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested file was not found on this server.</p>
</body></html>
EOD;
    die($r);
}
