<?php
require VIEWS_PATH . 'Navbar/StudentNavbar.php';
?>
<html>
<head>
    <link rel="stylesheet" href="Views/General.css">
    <style>
        /* Keep your styles for consistency */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 100000;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 5px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            margin: 0;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover, .close-modal:focus {
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>
<h1 class="titre">Groupes</h1>

<?php if ($classes): ?>
    <?php foreach ($classes as $class): ?>
        <?php
        $nom = htmlspecialchars($class->nom);
        $numero = htmlspecialchars($class->numero);
        $description = htmlspecialchars($class->description);
        $groupStudents = $studentsByGroup[$class->id] ?? [];
        ?>
        <details>
            <summary><strong><?= $nom ?></strong></summary>
            <div style="margin-left: 20px; padding-top: 5px;">
                <p><strong>Numéro:</strong> <?= $numero ?></p>
                <p><strong>Description:</strong> <?= $description ?></p>

                <?php if (!empty($groupStudents)): ?>
                    <p><strong>Étudiants dans ce groupe :</strong></p>
                    <ul>
                        <?php foreach ($groupStudents as $student): ?>
                            <li>
                                <?= htmlspecialchars($student['nom'] . ' ' . $student['prenom']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun étudiant dans ce groupe.</p>
                <?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune classe disponible.</p>
<?php endif; ?>

<footer>
    @Copyright gestionCollege 2025
</footer>

</body>
</html>
