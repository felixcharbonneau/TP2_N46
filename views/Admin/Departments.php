<?php
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>

<html>
    <head>
        <link rel="stylesheet" href="/Views/General.css">
        <script>
            function loadDepartments(){
                fetch('/api/departments')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('Department-Data');
                    tableBody.innerHTML = '';
                    data.forEach(department => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${department.nom}</td>
                            <td>${department.code}</td>
                            <td>
                                <button class="open-modal edit image-button" onclick="openEditModal(${department.id}, '${department.nom}', '${department.code}')">
                                    <img src="/Views/images/pen.webp" alt="Modifier" class="edit-icon">
                                </button>
                                <button class=\"image-button\" onclick="deleteDepartment(${department.id})">
                                    <img src="/Views/images/trash.webp" alt="Supprimer" class="delete-icon">
                                </button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                })
            }
            function createDepartment() {
                const form = document.getElementById('addDepartmentForm');
                const data = {
                    nom: form.nom.value,
                    code: form.code.value
                }; 
            
                if (!data.nom || !data.code) {
                    alert("Veuillez remplir tous les champs.");
                    return;
                }
                fetch('/api/departments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                })
                .then(data => {
                    if (data.status === 204) {
                        loadDepartments();
                        closeAddModal(); 
                    } else if (data.status === 400) {
                        alert(data.error || 'Données invalides');
                    }else{
                        alert(data.error || 'Une erreur est survenue lors de la création du département.');
                    }
                })
                .catch(error => alert(error.message));
            }


            loadDepartments();
        </script>
    </head>

    <body>
        <h1 class="titre">Départements</h1>
        <!-- Options de recherche et d'ajout -->
        <div class="options">
            <div class="recherche" id="searchForm">
                <input type="text" id="user_input" name="query" required>
                <input onclick="rechercher()" class="recherche" type="submit" value="Recherche">
            </div>
            <!-- modal pour ajouter un département -->
            <button class="open-modal ajout">&#x2b;</button>
            <div class="modal-overlay" id="modalOverlay">
            <div class="modal">
                <button class="close-button" id="closeModal">X</button>
                <h2>Ajouter un département</h2>
                <form method="POST" action="/api/departments" id="addDepartmentForm">
                    <div>
                        <label for="nom">Nom:</label>
                        <input type="text" name="nom" id="nom" placeholder="Entrez le nom du département" required>
                    </div>
                    <div>
                        <label for="code">Code:</label>
                        <input type="text" name="code" id="code" placeholder="Entrez le code du département" required>
                    </div>
                    <button type="button" class="submit-button" onclick="createDepartment()">Ajouter un département</button>
                </form>
            </div>
            </div>
            <button onclick="loadDepartments()" class="reset">Supprimer la recherche</button>
        </div>
        
        <!-- Affichage des données -->
        <table class="donnees">
            <thead>
                <tr>
                <tr>
                    <th>Nom</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
                </tr>
            </thead>
            <tbody id="Department-Data">

            </tbody>
        </table>
        <div class="modal-overlay" id="editEtudiantModalOverlay">
            <!-- Modal pour modifier un département -->
            <div class="modal">
                <button class="close-button" id="closeEditDepartmentModal">X</button>
                <h2>Modifier le département</h2>
                <div class="form">
                <input type="hidden" name="id" id="editEtudiantId" value="">
                <div>
                        <label for="editNom">Nom:</label>
                        <input type="text" name="nom" id="editNom" placeholder="Entrez le nom" required>
                    </div>
                    <div>
                        <label for="editPrenom">Prénom:</label>
                        <input type="text" name="prenom" id="editPrenom" placeholder="Entrez le prénom" required>
                    </div>
                    <div>
                        <label for="editDateNaissance">Date de naissance:</label>
                        <input type="date" name="dateNaissance" id="editDateNaissance" required>
                    </div>
                    <div>
                        <label for="editpassword">Mot de passe:</label>
                        <input type="text" name="password">
                    </div>
                    <button onclick="updateStudent()" class="submit-button" style="margin-top: 15px;">Mettre à jour l'étudiant</button>
                </div>
            </div>
        </div>
        <footer>
            @Copyright gestionCollege 2025
        </footer>
        <script src="/Views/js/modals.js"></script>
    </body>

</html>