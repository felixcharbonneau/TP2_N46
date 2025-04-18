<?php
namespace routes;

// Liste des ressources disponibles
$apiResources = [
    'students' => [
        'controller' =>  \CONTROLLERS_PATH . 'StudentsApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ]
];


