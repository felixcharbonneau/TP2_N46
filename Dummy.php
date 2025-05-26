<?php
$host = 'db';
$dbname = 'u6223134_GestionGroupe';
$username = 'u6223134';
$password = '54I}[wgz!{mf';
$charset = 'utf8mb4';

$pepper = '23jiasf98A?&S&*dsnj21ASUIDhui12';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $database = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Display error - useful for debugging, remove on production
    echo "Connection failed: " . $e->getMessage();
    exit;
}


$tables = ['EtudiantGroupe', 'Groupe', 'Cours', 'Enseignant', 'Etudiant', 'Departement'];

try {
    // Disable foreign key checks to avoid constraint issues
    $database->exec('SET FOREIGN_KEY_CHECKS = 0;');

    foreach ($tables as $table) {
        $sql = "TRUNCATE TABLE $table";
        $database->exec($sql);
        echo "Données supprimées de la table $table.<br>";
    }

    // Re-enable foreign key checks
    $database->exec('SET FOREIGN_KEY_CHECKS = 1;');

    echo 'Suppression de toutes les données terminée.';
} catch (Exception $error) {
    echo 'Erreur lors de la suppression des données';
    echo '<br>';
    print_r($error);
    die();
}
// Admin par défaut
try {
    $sql = "INSERT INTO Admin (nom, prenom, email, password,salt, createdBy)
            VALUES (:nom, :prenom, :email, :password,:salt, :createdBy)";
    $stmt = $database->prepare($sql);

    $stmt->bindValue(':nom', "root");
    $stmt->bindValue(':prenom', "root");
    $salt = bin2hex(random_bytes(8));
    $password = password_hash("root" . $salt . $pepper, PASSWORD_DEFAULT);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':email', "root@root.com");
    $stmt->bindValue(':createdBy', "system");
    $stmt->bindValue(':salt', $salt);

    $stmt->execute();
} catch (Exception $error) {
    echo 'Erreur lors de la creation de l\'admin par défaut';
    echo '<br>';
    print_r($error);
    die();
}


// Inserting 60 sample departments into the Departement table
try {
    $sql = "INSERT INTO Departement (nom, code, description, createdBy) VALUES
    (:nom, :code, :description, :createdBy)";

    $stmt = $database->prepare($sql);

    $departments = [
        ['Informatique', 'INF', 'Département d\'Informatique', 'system'],
        ['Mathématiques', 'MAT', 'Département de Mathématiques', 'system'],
        ['Physique', 'PHY', 'Département de Physique', 'system'],
        ['Chimie', 'CHM', 'Département de Chimie', 'system'],
        ['Biologie', 'BIO', 'Département de Biologie', 'system'],
        ['Génie Civil', 'GENCIV', 'Département de Génie Civil', 'system'],
        ['Génie Électrique', 'GENELEC', 'Département de Génie Électrique', 'system'],
        ['Génie Mécanique', 'GENMEC', 'Département de Génie Mécanique', 'system'],
        ['Architecture', 'ARC', 'Département d\'Architecture', 'system'],
        ['Économie', 'ECO', 'Département d\'Économie', 'system'],
        ['Gestion', 'GES', 'Département de Gestion', 'system'],
        ['Marketing', 'MKT', 'Département de Marketing', 'system'],
        ['Finance', 'FIN', 'Département de Finance', 'system'],
        ['Droit', 'LAW', 'Département de Droit', 'system'],
        ['Sciences Politiques', 'POL', 'Département de Sciences Politiques', 'system'],
        ['Psychologie', 'PSY', 'Département de Psychologie', 'system'],
        ['Philosophie', 'PHI', 'Département de Philosophie', 'system'],
        ['Histoire', 'HIS', 'Département d\'Histoire', 'system'],
        ['Sociologie', 'SOC', 'Département de Sociologie', 'system'],
        ['Linguistique', 'LIN', 'Département de Linguistique', 'system'],
        ['Lettres Modernes', 'LET', 'Département de Lettres Modernes', 'system'],
        ['Informatique Appliquée', 'INFAPP', 'Département d\'Informatique Appliquée', 'system'],
        ['Géologie', 'GEO', 'Département de Géologie', 'system'],
        ['Éducation', 'EDU', 'Département d\'Éducation', 'system'],
        ['Informatique de Gestion', 'INFGES', 'Département d\'Informatique de Gestion', 'system'],
        ['Génie Informatique', 'GENINF', 'Département de Génie Informatique', 'system'],
        ['Robotique', 'ROB', 'Département de Robotique', 'system'],
        ['Intelligence Artificielle', 'IA', 'Département d\'Intelligence Artificielle', 'system'],
        ['Cybersécurité', 'CYB', 'Département de Cybersécurité', 'system'],
        ['Jeux Vidéo', 'JV', 'Département de Jeux Vidéo', 'system'],
        ['Statistiques', 'STAT', 'Département de Statistiques', 'system'],
        ['Astronomie', 'AST', 'Département d\'Astronomie', 'system'],
        ['Biochimie', 'BIOCH', 'Département de Biochimie', 'system'],
        ['Pharmacie', 'PHAR', 'Département de Pharmacie', 'system'],
        ['Médecine', 'MED', 'Département de Médecine', 'system'],
        ['Chirurgie', 'CHI', 'Département de Chirurgie', 'system'],
        ['Odontologie', 'ODO', 'Département d\'Odontologie', 'system'],
        ['Infirmier', 'INFIR', 'Département des Sciences Infirmières', 'system'],
        ['Nutrition', 'NUT', 'Département de Nutrition', 'system'],
        ['Agronomie', 'AGR', 'Département d\'Agronomie', 'system'],
        ['Foresterie', 'FOR', 'Département de Foresterie', 'system'],
        ['Pêche', 'PEC', 'Département de Pêche', 'system'],
        ['Tourisme', 'TOU', 'Département de Tourisme', 'system'],
        ['Art Dramatique', 'ARTD', 'Département d\'Art Dramatique', 'system'],
        ['Musique', 'MUS', 'Département de Musique', 'system'],
        ['Cinéma', 'CIN', 'Département de Cinéma', 'system'],
        ['Arts Plastiques', 'ARTP', 'Département des Arts Plastiques', 'system'],
        ['Mode', 'MOD', 'Département de Mode', 'system'],
        ['Journalisme', 'JOU', 'Département de Journalisme', 'system'],
        ['Communication', 'COM', 'Département de Communication', 'system'],
        ['Informatique Théorique', 'INFTH', 'Département d\'Informatique Théorique', 'system'],
        ['Design Graphique', 'DESG', 'Département de Design Graphique', 'system'],
        ['Réseaux', 'RES', 'Département des Réseaux Informatiques', 'system'],
        ['Base de Données', 'BDD', 'Département des Bases de Données', 'system'],
        ['Cloud Computing', 'CLOUD', 'Département de Cloud Computing', 'system'],
        ['Développement Web', 'WEB', 'Département de Développement Web', 'system'],
        ['Systèmes Embarqués', 'SYS', 'Département des Systèmes Embarqués', 'system']
    ];

    foreach ($departments as $dept) {
        $stmt->execute([
            ':nom' => $dept[0],
            ':code' => $dept[1],
            ':description' => $dept[2],
            ':createdBy' => $dept[3]
        ]);
    }

    echo "Insertion réussie pour les 60 départements.";
} catch (Exception $error) {
    echo 'Erreur lors de l\'insertion des départements';
    echo '<br>';
    print_r($error);
    die();
}

$courses = [
    ['Mathematics', 'MATH101', 'Calculus and Analytical Geometry'],
    ['Physics', 'PHYS102', 'Fundamentals of Physics'],
    ['Computer Science', 'CS103', 'Introduction to Programming'],
    ['Biology', 'BIO104', 'Cell Biology and Genetics'],
    ['Chemistry', 'CHEM105', 'General Chemistry'],
    ['English', 'ENG106', 'English Composition'],
    ['History', 'HIST107', 'World History'],
    ['Psychology', 'PSY108', 'Introduction to Psychology'],
    ['Economics', 'ECON109', 'Principles of Microeconomics'],
    ['Political Science', 'POL110', 'Introduction to Political Science'],
    ['Sociology', 'SOC111', 'Introduction to Sociology'],
    ['Philosophy', 'PHIL112', 'Ethics and Moral Philosophy'],
    ['Statistics', 'STAT113', 'Probability and Statistics'],
    ['Finance', 'FIN114', 'Financial Management'],
    ['Marketing', 'MKT115', 'Principles of Marketing'],
    ['Business', 'BUS116', 'Business Administration'],
    ['Education', 'EDU117', 'Education and Learning Theories'],
    ['Engineering', 'ENGR118', 'Introduction to Engineering'],
    ['Nursing', 'NUR119', 'Fundamentals of Nursing'],
    ['Medicine', 'MED120', 'Medical Terminology'],
    ['Art', 'ART121', 'Art History'],
    ['Music', 'MUS122', 'Music Theory'],
    ['Theater', 'THE123', 'Introduction to Theater'],
    ['Law', 'LAW124', 'Introduction to Law'],
    ['Journalism', 'JOU125', 'Fundamentals of Journalism'],
    ['Architecture', 'ARC126', 'Architectural Design'],
    ['Environmental Science', 'ENV127', 'Introduction to Environmental Science'],
    ['Geography', 'GEO128', 'Physical Geography'],
    ['Anthropology', 'ANT129', 'Cultural Anthropology'],
    ['Linguistics', 'LIN130', 'Introduction to Linguistics'],
    ['Pharmacy', 'PHA131', 'Pharmaceutical Chemistry'],
    ['Dentistry', 'DEN132', 'Oral Health and Dentistry'],
    ['Astronomy', 'AST133', 'Exploring the Universe'],
    ['Geology', 'GEO134', 'Earth and Planetary Science'],
    ['Zoology', 'ZOO135', 'Animal Biology'],
    ['Botany', 'BOT136', 'Plant Biology'],
    ['Public Health', 'PH137', 'Introduction to Public Health'],
    ['Information Technology', 'IT138', 'Database Management Systems'],
    ['Cybersecurity', 'CYB139', 'Network Security'],
    ['Artificial Intelligence', 'AI140', 'Fundamentals of AI'],
    ['Game Design', 'GD141', 'Game Development Principles'],
    ['Data Science', 'DS142', 'Machine Learning Basics'],
    ['Aerospace Engineering', 'AE143', 'Fundamentals of Aerospace'],
    ['Automotive Engineering', 'AUTO144', 'Vehicle Design and Dynamics'],
    ['Civil Engineering', 'CIV145', 'Structural Analysis'],
    ['Mechanical Engineering', 'MECH146', 'Thermodynamics'],
    ['Electrical Engineering', 'ELEC147', 'Circuit Theory'],
    ['Chemical Engineering', 'CHE148', 'Process Engineering'],
    ['Biotechnology', 'BIO149', 'Genetic Engineering'],
    ['Nanotechnology', 'NANO150', 'Introduction to Nanoscience'],
    ['Renewable Energy', 'RE151', 'Solar and Wind Energy Systems'],
    ['Robotics', 'ROB152', 'Introduction to Robotics'],
    ['Marine Biology', 'MB153', 'Ocean Life Exploration'],
    ['Archaeology', 'ARC154', 'Ancient Civilizations'],
    ['Meteorology', 'MET155', 'Weather and Climate'],
    ['Social Work', 'SW156', 'Community and Family Services'],
    ['Criminology', 'CRM157', 'Crime and Criminal Behavior'],
    ['Forensic Science', 'FS158', 'Crime Scene Investigation'],
    ['Sports Science', 'SS159', 'Human Physiology and Sports'],
    ['Nutrition', 'NUT160', 'Human Nutrition and Dietetics']
];

try {
    $sql = "INSERT INTO Cours (nom, numero, idDepartement, description, createdBy) VALUES (:nom, :numero, :idDepartement, :description, :createdBy)";
    $stmt = $database->prepare($sql);

    foreach ($courses as $i => $course) {
        $idDepartement = rand(1, 10); // Random department ID from 1 to 10
        $stmt->execute([
            ':nom' => $course[0],
            ':numero' => $course[1],
            ':idDepartement' => $idDepartement,
            ':description' => $course[2],
            ':createdBy' => 'system'
        ]);
    }

    echo "Insertion réussie pour les 120 cours.";
} catch (Exception $error) {
    echo 'Erreur lors de l\'insertion des cours';
    echo '<br>';
    print_r($error);
    die();
}

try {
    $sql = "INSERT INTO Etudiant (da, nom, prenom, dateNaissance, email, dateInscription, password,salt, createdBy) VALUES
    (:da, :nom, :prenom, :dateNaissance, :email, :dateInscription, :password,:salt, :createdBy)";

    $stmt = $database->prepare($sql);

    // Sample first and last names
    $firstNames = ["Alice", "Bob", "Charlie", "David", "Eva", "Frank", "Grace", "Hannah", "Ivy", "Jack", "Kate", "Liam", "Mia", "Nathan", "Olivia", "Paul", "Quincy", "Rachel", "Sam", "Tina", "Ursula", "Victor", "Wendy", "Xander", "Yvonne", "Zach"];
    $lastNames = ["Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "García", "Rodriguez", "Martínez", "Hernández", "Lopez", "González", "Wilson", "Anderson", "Thomas", "Taylor", "Moore", "Jackson", "Martin", "Lee", "Perez", "Thompson", "White", "Harris"];

    for ($i = 1; $i <= 100; $i++) {
        $da = 1234567;//Changer
        $nom = $lastNames[array_rand($lastNames)]; 
        $prenom = $firstNames[array_rand($firstNames)]; 
        $dateNaissance = date('Y-m-d', strtotime("-" . rand(18, 25) . " years")); 
        $email = "$da$i@etu.cegep-lanaudiere.qc.ca"; 
        $dateInscription = date('Y-m-d'); 
        $salt = bin2hex(random_bytes(8));
        $password = password_hash(date('Ymd', strtotime($dateNaissance)).$salt.$pepper, PASSWORD_DEFAULT); 

        $stmt->execute([
            ':da' => $da,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':dateNaissance' => $dateNaissance,
            ':email' => $email,
            ':dateInscription' => $dateInscription,
            ':password' => $password,
            ':salt' => $salt,
            ':createdBy' => 'system'
        ]);
    }

    echo "Insertion réussie pour les 200 étudiants.";
} catch (Exception $error) {
    echo 'Erreur lors de l\'insertion des étudiants';
    echo '<br>';
    print_r($error);
    die();
}
// Insertion of teacher
try {
    $teacherSql = "INSERT INTO Enseignant (nom, prenom, email, dateNaissance, dateEmbauche, password, salt, createdBy, idDepartement) 
                    VALUES (:nom, :prenom, :email, :dateNaissance, :dateEmbauche, :password, :salt, :createdBy, :idDepartement)";
    $teacherStmt = $database->prepare($teacherSql);

    // Sample first and last names for random selection
    $firstNames = ["Alice", "Bob", "Charlie", "David", "Eva", "Frank", "Grace", "Hannah", "Ivy", "Jack", "Kate", "Liam", "Mia", "Nathan", "Olivia", "Paul", "Quincy", "Rachel", "Sam", "Tina", "Ursula", "Victor", "Wendy", "Xander", "Yvonne", "Zach"];
    $lastNames = ["Smith", "Johnson", "Williams", "Brown", "Jones", "Miller", "Davis", "García", "Rodriguez", "Martínez", "Hernández", "Lopez", "González", "Wilson", "Anderson", "Thomas", "Taylor", "Moore", "Jackson", "Martin", "Lee", "Perez", "Thompson", "White", "Harris"];

    for ($i = 1; $i <= 30; $i++) {
        $teacherNom = $lastNames[array_rand($lastNames)];
        $teacherPrenom = $firstNames[array_rand($firstNames)];
        $teacherEmail = strtolower($teacherNom . "." . $teacherPrenom . "@example.com");  // Ensure email is unique
        $teacherDateNaissance = date('Y-m-d', strtotime("-" . rand(25, 45) . " years"));
        $teacherDateEmbauche = date('Y-m-d');
        $teachersalt = bin2hex(random_bytes(8));  // Salt for teacher
        $teacherPassword = password_hash("password" . $teachersalt . $pepper, PASSWORD_DEFAULT); // Hash the password with salt + pepper

        // Execute the insert
        $teacherStmt->execute([
            ':nom' => $teacherNom,
            ':prenom' => $teacherPrenom,
            ':email' => $teacherEmail,
            ':dateNaissance' => $teacherDateNaissance,
            ':dateEmbauche' => $teacherDateEmbauche,
            ':password' => $teacherPassword,
            ':createdBy' => 'system',
            ':idDepartement' => rand(1, 10),  // Assign a random department id (1-10)
            ':salt' => $teachersalt
        ]);
        
        echo "Teacher {$teacherNom} {$teacherPrenom} inserted successfully.\n";
    }

} catch (Exception $error) {
    echo 'Error during teacher creation: ';
    print_r($error);
    die();
}


// Generate 50 random groups
for ($i = 1; $i <= 50; $i++) {
    // Random group name, you can customize this as needed
    $groupName = 'Group ' . $i;
    
    // Random number for group number (for simplicity, we use $i as the number)
    $groupNumber = $i;
    
    // Random course ID and teacher ID (make sure these IDs exist in your Cours and Enseignant tables)
    $randomCourseId = rand(1, 50); // Assuming you have 120 courses
    $randomTeacherId = rand(1, 10); // Assuming you have 100 teachers
    
    // Random description (customize as per your needs)
    $description = "Description for group " . $groupName;

    try {
        $sql = "INSERT INTO Groupe (nom, numero, idCours, idEnseignant, description, createdBy)
                VALUES (:nom, :numero, :idCours, :idEnseignant, :description, :createdBy)";
        
        $stmt = $database->prepare($sql);
        
        // Bind values
        $stmt->bindValue(':nom', $groupName);
        $stmt->bindValue(':numero', $groupNumber);
        $stmt->bindValue(':idCours', $randomCourseId);
        $stmt->bindValue(':idEnseignant', $randomTeacherId);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':createdBy', "system");  
        // Execute the insertion
        $stmt->execute();
        
        echo "Group {$groupName} inserted successfully.\n";
    } catch (Exception $error) {
        echo 'Error inserting group ' . $groupName;
        echo '<br>';
        print_r($error);
        die();
    }
}


try {
    $stmt = $database->query("SELECT id FROM Etudiant");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $studentIds = array_column($students, 'id');
} catch (Exception $error) {
    echo 'Erreur lors de la récupération des étudiants';
    echo '<br>';
    print_r($error);
    die();
}

// Get all group IDs (to assign students to the groups)
try {
    $stmt = $database->query("SELECT id FROM Groupe");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $groupIds = array_column($groups, 'id');
} catch (Exception $error) {
    echo 'Erreur lors de la récupération des groupes';
    echo '<br>';
    print_r($error);
    die();
}

// Assign 30 unique students to each group
foreach ($groupIds as $groupId) {
    // Shuffle students to randomize selection
    shuffle($studentIds);
    
    // Get the first 30 students for this group
    $selectedStudents = array_slice($studentIds, 0, 30);

    // Insert students into EtudiantGroupe table
    foreach ($selectedStudents as $studentId) {
        try {
            $sql = "INSERT INTO EtudiantGroupe (idEtudiant, idGroupe, createdBy) 
                    VALUES (:idEtudiant, :idGroupe, :createdBy)";
            
            $stmt = $database->prepare($sql);
            
            // Bind values
            $stmt->bindValue(':idEtudiant', $studentId);
            $stmt->bindValue(':idGroupe', $groupId);
            $stmt->bindValue(':createdBy', "system");
            
            // Execute the insertion
            $stmt->execute();
            
            echo "Student {$studentId} added to group {$groupId}.\n";
        } catch (Exception $error) {
            echo 'Error inserting student ' . $studentId . ' into group ' . $groupId;
            echo '<br>';
            print_r($error);
            die();
        }
    }
}

?>
