<?php
    namespace views;
    $errorMessage = isset($_GET['error']) ? "Courriel ou mot de passe incorrect" : null;
?>


<!-- page de connexion -->
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/Views/css/General.css">
</head>
<body class="connexion">
    <nav></nav>
    <div class=".connexionContainer">
        <div id="connexion">
            <h1>Connexion</h1>
            <form style="gap:20px;position:relative" action="/Authentification/login" method="POST">
                <div class="role" style="display:absolute;grid-column:span 2;margin:auto">
                    <input type="radio" name="role" value="Admin" id="admin">
                    <label for="admin">Admin</label>
                    <input type="radio" name="role" value="Enseignant" id="enseignant">
                    <label for="enseignant">Enseignant</label>
                    <input type="radio" name="role" value="Etudiant" id="etudiant" checked>
                    <label for="etudiant">Etudiant</label>
                </div>
                <label for="email">Courriel:</label>
                <input required type="email" name="email">
                <label for="password">Mot de Passe:</label>
                <input required type="password" id="password" name="password">
                <input type="submit" value="Se Connecter">
                <?php if (isset($errorMessage)): ?>
                    <span style="position:absolute;top:180px" class="error-message"><?php echo $errorMessage; ?></span>
            <?php endif; ?>
            </form> 
            <a style="margin-top:10px" href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Mot de passe oublié?</a>
        </div>
    </div>
</body>
</html>