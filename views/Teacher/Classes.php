<?php require VIEWS_PATH . 'Navbar/TeacherNavbar.php'; ?>

<html>
<head>
    <link rel="stylesheet" href="/Views/General.css">
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

                                <form method="POST" action="/classes/removeStudent" style="display:inline;">
                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']) ?>">
                                    <input type="hidden" name="group_id" value="<?= htmlspecialchars($class->id) ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo isset($removeToken) ? htmlspecialchars($removeToken) : ''; ?>">
                                    <button class="image-button" type="submit" onclick="return confirm('Supprimer cet étudiant du groupe ?');">
                                        <img src="/Views/images/trash.webp" alt="Supprimer" class="delete-icon">
                                    </button>
                                </form>
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
