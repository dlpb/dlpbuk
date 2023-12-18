<?php
    function getData($path = "."): array {
        $data = [];
        $files = glob($path.'/*.json');
        foreach($files as $file) {
            $strData = file_get_contents($file);

            $json = json_decode($strData);

            $nameParts = explode("/", str_split($file, strlen($file) - 5)[0]);
            $name = "";
            if(sizeof($nameParts) === 1){
                $name = htmlspecialchars($nameParts[0]);
            } else {
                $name = $nameParts[sizeof($nameParts)-1];
            }
            if(!is_null($json)){
                if(!isset($json->section)){
                    $json->section = new stdClass();
                    $json->section->title = $name;
                    $json->section->order = PHP_INT_MAX;
                }

                $data[$name] = $json;
            }
        }

        uasort($data, function($a, $b) {
            return $a->section->order - $b->section->order;
        });

        return $data;
    }
?>