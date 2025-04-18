<?php
namespace routes;

// Liste des ressources disponibles
$apiResources = [
    'students' => [
        'controller' =>  \CONTROLLERS_PATH . 'StudentsApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ],
    'departments' => [
        'controller' =>  \CONTROLLERS_PATH . 'DepartmentsApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ],
    'teachers' => [
        'controller' =>  \CONTROLLERS_PATH . 'TeachersApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ],
    'classes' => [
        'controller' =>  \CONTROLLERS_PATH . 'ClassesApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ],
    'courses' => [
        'controller' =>  \CONTROLLERS_PATH . 'CoursesApi',
        'methods' => ['GET', 'POST', 'PUT', 'DELETE']
    ]
];


