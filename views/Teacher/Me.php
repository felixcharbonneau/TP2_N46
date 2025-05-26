<?php
include VIEWS_PATH . 'Navbar/TeacherNavbar.php';
?>
<html>
<head>
    <link rel="stylesheet" href="views/General.css">
</head>
<body>
<h1 class="titre">Mon Compte</h1>

<?php if (isset($teacher)): ?>
    <div class="teacher-info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($teacher->nom) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($teacher->prenom) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($teacher->email) ?></p>
        <p><strong>Date de naissance :</strong> <?= htmlspecialchars($teacher->dateNaissance) ?></p>
        <p><strong>Date d'embauche :</strong> <?= htmlspecialchars($teacher->dateEmbauche) ?></p>
        <?php if ($department): ?>
            <p><strong>Département :</strong> <?= htmlspecialchars($department->nom) ?></p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p>Informations sur l'enseignant non disponibles.</p>
<?php endif; ?>
</body>
</html>
