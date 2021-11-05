<?php


$data = "jack'n";




function transferred_meaning($data)
{

    if (strstr($data, "'")) {
        $data = str_replace("'", "\'", $data);
    }
    return $data;

}


var_dump(transferred_meaning($data));