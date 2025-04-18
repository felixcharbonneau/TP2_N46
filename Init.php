<!-- Fichier de génération de la bd -->

<?php
$dsn = "mysql:host=db;dbname=GestionGroupe;charset=utf8mb4";
$database = new \PDO($dsn, "test", "test", [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
]);

//Admin
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Admin`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(50) NOT NULL,
            `prenom` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`)
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Admin';
    echo '<br>';
    print_r($error);
    die();
}
//Etudiants
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Etudiant`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `da` VARCHAR(7) NOT NULL,
            `nom` VARCHAR(50) NOT NULL,
            `prenom` VARCHAR(50) NOT NULL,
            `dateNaissance` DATE NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `dateInscription` DATE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`)
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Etudiants';
    echo '<br>';
    print_r($error);
    die();
}

//Departements
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Departement`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(50) NOT NULL,
            `code` VARCHAR(50) NOT NULL,
            `description` TEXT NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`)
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Departements';
    echo '<br>';
    print_r($error);
    die();
}

//Enseignants
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Enseignant`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(50) NOT NULL,
            `prenom` VARCHAR(50) NOT NULL,
            `dateNaissance` DATE NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `dateEmbauche` DATE NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `coordonateur` BOOLEAN NOT NULL,
            `idDepartement` INT UNSIGNED DEFAULT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`),
            FOREIGN KEY(`idDepartement`) REFERENCES `Departement`(`id`) ON DELETE SET NULL
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Enseignants';
    echo '<br>';
    print_r($error);
    die();
}
//Cours
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Cours`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(50) NOT NULL,
            `numero` VARCHAR(50) NOT NULL,
            `idDepartement` INT UNSIGNED DEFAULT NULL,
            `description` TEXT NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`),
            FOREIGN KEY(`idDepartement`) REFERENCES `Departement`(`id`) ON DELETE SET NULL
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Cours';
    echo '<br>';
    print_r($error);
    die();
}

//Groupe
try{
    $created = $database->exec(
        "CREATE TABLE IF NOT EXISTS `Groupe`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(50) NOT NULL,
            `numero` INT NOT NULL,
            `idCours` INT UNSIGNED NOT NULL,
            `idEnseignant` INT UNSIGNED DEFAULT NULL,
            `description` TEXT NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`),
            CONSTRAINT `fk_Groupe_Enseignant` FOREIGN KEY(`idEnseignant`) REFERENCES `Enseignant`(`id`) ON DELETE SET NULL,	
            FOREIGN KEY(`idCours`) REFERENCES `Cours`(`id`) ON DELETE CASCADE
        );"
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Groupe';
    echo '<br>';
    print_r($error);
    die();
}

//table de liaison entre groupe et etudiant
try{
    $created = $database->exec(
        <<<SQL
        CREATE TABLE IF NOT EXISTS `EtudiantGroupe`(
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `idEtudiant` INT UNSIGNED NOT NULL,
            `idGroupe` INT UNSIGNED NOT NULL,
            `createdBy` VARCHAR(100),
            `createdOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedOn` DATE NOT NULL DEFAULT CURRENT_DATE(),
            `modifiedBy` VARCHAR(100),
            PRIMARY KEY(`id`),
            CONSTRAINT `fk_EtudiantGroupe_Etudiant` FOREIGN KEY(`idEtudiant`) REFERENCES Etudiant(`id`) ON DELETE CASCADE,
            CONSTRAINT `fk_EtudiantGroupe_Groupe` FOREIGN KEY(`idGroupe`) REFERENCES Groupe(`id`) ON DELETE CASCADE
        );
        SQL
    );
}catch (Exception $error) {
    echo 'Erreur lors de la creation de la table Etudiant';
    echo '<br>';
    print_r($error);
    die();
}

// Admin par défaut
try {
    $sql = "INSERT INTO Admin (nom, prenom, email, password, createdBy)
            VALUES (:nom, :prenom, :email, :password, :createdBy)";
    $stmt = $database->prepare($sql);

    $stmt->bindValue(':nom', "root"); 
    $stmt->bindValue(':prenom', "root"); 
    $password = password_hash("root", PASSWORD_DEFAULT);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':email', "root@root.com");
    $stmt->bindValue(':createdBy', "system"); 

    $stmt->execute();
} catch (Exception $error) {
    echo 'Erreur lors de la creation de l\'admin par défaut';
    echo '<br>';
    print_r($error);
    die();
}

//Supprime un groupe lorsque son cours correspondant est supprimé
try {
    $sql = "
        CREATE TRIGGER IF NOT EXISTS delete_groups_when_course_deleted
        AFTER DELETE ON Cours
        FOR EACH ROW
        BEGIN
            DELETE FROM Groupe WHERE idCours = OLD.id;
        END;
    ";
    $database->exec($sql);
} catch (Exception $error) {
    echo 'Erreur lors de la creation du trigger pour la suppression des groupes liés au cours';
    echo '<br>';
    print_r($error);
    die();
}

$tables = [
    'Etudiant',
    'Departement',
    'Enseignant',
    'Cours',
    'Groupe',
    'EtudiantGroupe'
];

foreach ($tables as $table) {
    try {
        $sql = "
            CREATE TRIGGER IF NOT EXISTS update_{$table}_modifiedOn
            BEFORE UPDATE ON {$table}
            FOR EACH ROW
            BEGIN
                SET NEW.modifiedOn = CURRENT_DATE();
            END;
        ";
        $database->exec($sql);
    } catch (Exception $error) {
        echo "Erreur lors de la creation du trigger pour la table {$table}";
        echo '<br>';
        print_r($error);
        die();
    }

}