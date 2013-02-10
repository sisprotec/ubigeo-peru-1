<?php
$service_doc['lugar|place'] =  array(
    'en' => array (
        'patron' => '/ubigeo/place/DEPARTMENT[/PROVINCE[/DISTRICT]]',
        'description' => 'Searches for the ubigeo codes (reniec, inei) for the place given as '
                         .'/DEPARTMENT[/PROVINCE[/DISTRICT]] (the last 2 parameters are optional)',
    ),
    'es' => array(
        'patron' => '/ubigeo/lugar/DEPARTAMENTO[/PROVINCIA][/DISTRITO]',
        'descripción' => 'Busca los códigos de ubigeo (reniec, inei) para el lugar indicado por '
                        .'el DEPARTAMENTO/PROVINCIA/DISTRITO (los últimos dos parámetros son opcionales)',
    )
);

$flugar = function ($dpt, $prov='', $dist='') use ($app, $db) {
    $stm = $db->prepare('select * from ubigeo_equiv where nombre_completo = :lugar');
    $stm->bindValue(':lugar', strtoupper("${dpt}/${prov}/${dist}"), PDO::PARAM_STR);
    $stm->execute();
    $res = $stm->fetchAll();
    if (count($res) === 0) {
        $app->getLog()->error('3:badlocation:'.$dpt.'/'.$prov.'/'.$dist);
        $res = array('error'=>3, 'msg'=>'no existe el lugar que ha indicado');
    }
    echo json_encode(array(
                    $app->request()->getResourceUri() => $res
                ));
};

$app->get('/ubigeo/lugar/:dpt(/:prov(/:dist))', $flugar)->name('lugar');
$app->get('/ubigeo/place/:dpt(/:prov(/:dist))', $flugar)->name('place');
