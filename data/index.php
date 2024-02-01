<?php
    include_once("data.php");
    
    header("Content-type: application/json");
    header("Cache-control: No-cache, no-store, must-revalidate");

    echo json_encode(getData());
?>