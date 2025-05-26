<?php
namespace views;
?>
<!-- page d'erreur lorsque l'utilisateur n'a pas les droits d'accès -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="views/css/General.css">
    <title>Erreur de droits</title>
</head>
<body class="connexion">
    <nav></nav>
    <div class="connexionContainer"  style="text-align:center;width:660px;margin:auto;display:flex;flex-direction:column;align-items:center;justify-content:center">
        <div id="connexion" style="width:100%;margin:auto;height:200px;">
            <h1>Erreur de droits</h1>
            <p>Vous n'avez pas les droits nécessaires pour accéder à cette page.</p>
            <a href="/connexion">Retour à la page de connexion</a>
        </div>
    </div>
</body>
</html>