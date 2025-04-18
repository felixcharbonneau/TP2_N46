<?php
namespace models;

/**
 * Classe abstraite pour une personne
 */
abstract class User {
    public int $id;//<l'identifiant de la personne
    public string $nom;//<le nom de la personne
    public string $prenom;//<le prénom de la personne
    public string $email;//<le courriel de la personne
    public string $dateNaissance;//<la date de naissance de la personne
    public string $createdBy;//<l'utilisateur ayant créé la personne
    public string $modifiedBy;//<l'utilisateur ayant modifié la personne

    /**
     * Constructeur de la personne
     * @param int $id l'identifiant de la personne
     * @param string $nom le nom de la personne
     * @param string $prenom le prénom de la personne
     * @param string $email le courriel de la personne
     * @param string $dateNaissance la date de naissance de la personne
     * @param string $createdBy l'utilisateur ayant créé la personne
     * @param string $modifiedBy l'utilisateur ayant modifié la personne
     */
    public function __construct(
        int $id,
        string $nom,
        string $prenom,
        string $email,
        string $dateNaissance,
        string $createdBy,
        ?string $modifiedBy = ''
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->dateNaissance = $dateNaissance;
        $this->createdBy = $createdBy;
        $this->modifiedBy = $modifiedBy ?? ''; 
    }
    /**
     * Vérfie si le mot de passe est correct pour la personne
     * @param string $password le mot de passe à vérifier
     */
    abstract function connexion($password);
    /**
     * Selectionne une personne par son email
     * @param string $email l'email de la personne
     */
    abstract static function selectByEmail($email);




}