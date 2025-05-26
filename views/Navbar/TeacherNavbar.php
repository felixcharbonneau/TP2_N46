<!-- Barre de navigation de l'admin -->
<head>
    <title>Gestion de College</title>
    <link rel="stylesheet" href="views/css/General.css">
    <link rel="stylesheet" href="views/css/Navbar.css">
</head>
<nav>
    <div id="options">
        <a class="link" href="Home">Accueil</a>
        <a class="link" href="Classes">Groupes</a>
        <a class="link" href="Me">Mon compte</a>
    </div>

    <img id="icon" src="views/Images/utilisateur.svg">
    <div id="slidingDiv" class="hidden">
        <h5>Connexion:</h5>
        <h6>Bonjour: <?php echo $_SESSION['user_email'] ?></h6>
        <h6>Vous êtes un: <?php echo $_SESSION['user_role'] ?></h6>
        <form action="Authentification/disconnect" method="GET">
            <button class="disconnect" type="submit">
                <img height="50px" src="views/Images/disconnect.png"></img>
            </button>
        </form>
    </div>
    <script src="views/js/UserMenu.js"></script>
</nav>