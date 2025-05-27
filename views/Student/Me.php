<?php
include VIEWS_PATH . 'Navbar/StudentNavbar.php';
?>
<html>
<head>
    <link rel="stylesheet" href="views/General.css">
</head>
<body>
<h1 class="titre">Mon Compte</h1>

<?php if (isset($student)): ?>
    <div class="student-info">
        <p><strong>Nom :</strong> <?= htmlspecialchars($student->nom) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($student->prenom) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($student->email) ?></p>
        <p><strong>Date de naissance :</strong> <?= htmlspecialchars($student->dateNaissance) ?></p>
        <p><strong>Date d'inscription :</strong> <?= htmlspecialchars($student->dateInscription) ?></p>
        <p><strong>DA :</strong> <?= htmlspecialchars($student->da) ?></p>
    </div>
<?php else: ?>
    <p>Informations sur l'étudiant non disponibles.</p>
<?php endif; ?>

</body>
</html>
