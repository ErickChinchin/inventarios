<?php
header('Content-Type: application/json');
if(isset($_GET["dni"]) && $_GET["dni"] != ""){
    $clients = PersonData::getClientsByDni($_GET["dni"]);
    $result = array();
    foreach($clients as $c){
        $result[] = array(
            "id"   => $c->id,
            "name" => $c->name . " " . $c->lastname,
            "dni"  => $c->dni
        );
    }
    echo json_encode($result);
} else {
    echo json_encode(array());
}
?>
