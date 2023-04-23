<?php
    $data = [];
    $files = glob('*.json');
    foreach($files as $file) {
        $strData = file_get_contents($file);
        $name = str_split($file, strlen($file) - 5)[0];
        $data[$name] = json_decode($strData);
    }

    header("Content-type: application/json");
    header("Cache-control: No-cache, no-store, must-revalidate");

    echo json_encode($data);
?>