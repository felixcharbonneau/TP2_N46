<?php
namespace controllers;
use models\Department;

class DepartmentsApi {

    public function __construct() {
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
    }
    public function getDepartments(){
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        if($query){
            $departments = Department::getAll($query);
        }else{
            $departments = Department::getAll();
        }
        return json_encode($departments);
    }
    public function getDepartment($id){
        $department = Department::get($id);
        return json_encode($department);
    }
    public function createDepartment(){
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nom']) || !isset($data['description'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['description']) > 255) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou la description ne doit pas dépasser 50 ou 255 caractères respectivement']);
        }
        $department = Department::create($data);
        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la création du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }
    public function updateDepartment($id){
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nom']) || !isset($data['description'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['description']) > 255) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou la description ne doit pas dépasser 50 ou 255 caractères respectivement']);
        }
        $department = Department::update($id, $data);
        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la mise à jour du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }
    public function deleteDepartment($id){
        $department = Department::delete($id);
        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la suppression du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }
    public function rechercher(){
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        if($query){
            $departments = Department::getAll($query);
        }else{
            $departments = Department::getAll();
        }
        return json_encode($departments);
    }
}