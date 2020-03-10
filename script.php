<?php

$path = $argv[1] ?? './';
$quality = (int) $argv[2] ?? 75;
$directory = new \RecursiveDirectoryIterator($path);
$iterator = new \RecursiveIteratorIterator($directory);

$count = 0;
foreach ($iterator as $info) {
    $mimeType = mime_content_type($info->getPathname());
    if ($mimeType == 'image/jpeg') {
        $count++;
        optimizeImage($info->getPathname(), $quality);
    }
}

echo "Complete for " . $count . ' jpg images' . PHP_EOL;

function optimizeImage($originalPath, $quality = 75) {
    $tmpFile = '/tmp/' . uniqid('opt');

    $cJpegCommand = "/opt/mozjpeg/bin/cjpeg -outfile {$tmpFile} -progressive -optimize -quality {$quality} '{$originalPath}' 2>&1";

    exec(
        $cJpegCommand,
        $output,
        $returnVar
    );
    if ($returnVar) {
        echo 'Image optimization error: ' . implode("\n", $output) . PHP_EOL;
        echo $cJpegCommand . PHP_EOL;
    }
    else {
        copy($tmpFile, $originalPath);
        unlink($tmpFile);
    }

    echo "Complete image " . $originalPath . PHP_EOL;
}
