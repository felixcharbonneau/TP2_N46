<?php
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>
<html>
    <head>
        <link rel="stylesheet" href="/Views/General.css">
        <script>
            function showUsers(data) {
                const tableBody = document.getElementById('Student-Data');
                        tableBody.innerHTML = '';
                        data.forEach(student => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${student.da}</td>
                                <td>${student.nom} ${student.prenom}</td>
                                <td>${student.email}</td>
                                <td>
                                    <button class="open-modal edit image-button" onclick="openEditModal(${student.id}, '${student.nom}', '${student.prenom}', '${student.dateNaissance}')">
                                        <img src="/Views/Images/pen.webp">
                                    </button>
                                    <button type=\"submit\" class=\"image-button\" onclick=\"deleteStudent(${student.id})\">
                                        <img src=\"/Views/Images/trash.webp\" alt=\"Icon\">
                                    </button>
                                </td>`;
                            tableBody.appendChild(row);
                        });
            }
            /**
             * Fonction pour charger les étudiants depuis l'API et les afficher dans la table
             */
            function loadStudents(){
                fetch('/api/students')
                    .then(response => response.json())
                    .then(data => {
                        showUsers(data);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        const tableBody = document.getElementById('Student-Data');
                        tableBody.innerHTML = `<tr><td colspan="4">Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.</td></tr>`;
                    });
            }
            /**
             * Fonction pour ouvrir le modal d'édition d'un étudiant
             * @param {number} id - L'ID de l'étudiant à éditer
             * @param {string} nom - Le nom de l'étudiant
             * @param {string} prenom - Le prénom de l'étudiant
             * @param {string} dateNaissance - La date de naissance de l'étudiant
             */
            function openEditModal(id, nom, prenom, dateNaissance) {
                const editEtudiantId = document.getElementById('editEtudiantId');
                const editNom = document.getElementById('editNom');
                const editPrenom = document.getElementById('editPrenom');
                const editNaissance = document.getElementById('editDateNaissance');
                
                editEtudiantId.value = id;
                editNom.value = nom;
                editPrenom.value = prenom;
                const formattedDate = new Date(dateNaissance).toISOString().split('T')[0];
                editNaissance.value = formattedDate;
                
                const modal = document.querySelector('#editEtudiantModalOverlay');
                modal.style.display = 'block';
                
                document.getElementById('closeEditEtudiantModal').addEventListener('click', () => {
                    modal.style.display = 'none';
                });

                window.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }
            /**
             * Fonction pour la suppression d'un étudiant
             * @param {number} studentId - L'ID de l'étudiant à supprimer
             */
            function deleteStudent(studentId) {
                fetch(`/api/students/${studentId}`, {
                    method: 'DELETE'
                })
                .then(async response => {
                    if (response.status === 204) {
                        loadStudents();
                    } else {
                        let errorMsg = 'Erreur lors de la suppression';
                        throw new Error(errorMsg);
                    }
                })
                .catch(error => alert(error.message));
            }
            /**
             * Fonction pour mettretre à jour un étudiant
             * @param {number} id - L'ID de l'étudiant à mettre à jour
             */
            function updateStudent() {
                fetch(`/api/students/${id}`, {
                    method: 'PUT',
                })
                .then(response => {
                    if (response.status === 204) {
                        loadStudents();
                        closeEditModal();
                    } else {
                        throw new Error('Erreur lors de la mise à jour de l\'étudiant');
                    }

                    loadStudents();
                })
                .catch(error => alert(error.message));
            }
            /**
             * Fonction pour créer un nouvel étudiant
             */
            function createStudent() {
                const form = document.getElementById('addStudentForm');
                const data = {
                    nom: form.nom.value,
                    prenom: form.prenom.value,
                    dateNaissance: form.dateNaissance.value,
                }; 
            
                // Check if all fields are filled
                if (!data.nom || !data.prenom || !data.dateNaissance) {
                    alert("Veuillez remplir tous les champs.");
                    return;
                }
            
                // Send data to the backend via fetch
                fetch('/api/students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                })
                .then(data => {
                    if (data.status === 204) {
                        loadStudents();
                        closeAddModal(); 
                    } else if (data.status === 400) {
                        alert(data.error || 'Données invalides');
                    }else{
                        alert(data.error || 'Une erreur est survenue lors de la création de l\'étudiant.');
                    }
                })
                .catch(error => alert(error.message));
            }
            function closeAddModal() {
                const modal = document.querySelector('#modalOverlay');
                modal.style.display = 'none';
            }
            loadStudents();

            function rechercher() {
                var query = document.getElementById('user_input').value;
                if (!query) {
                    alert('Veuillez entrer un terme de recherche.');
                    return;
                }
            
                fetch(`/api/students?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showUsers(data); 
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la recherche.');
                });
            }
        </script>
    </head>
    <body>
        <h1 class="titre">Étudiants</h1>

        <!-- Options de recherche et d'ajout -->
        <div class="options">
            <div class="recherche" id="searchForm">
                <input type="text" id="user_input" name="query" required>
                <input onclick="rechercher()" class="recherche" type="submit" value="Recherche">
            </div>
            <button class="open-modal ajout">&#x2b;</button>
            <div class="modal-overlay" id="modalOverlay">
            <div class="modal">
                <button class="close-button" id="closeModal">X</button>
                <h2>Ajouter un étudiant</h2>
                <form method="POST" action="/api/students" id="addStudentForm">
                    <div>
                        <label for="nom">Nom:</label>
                        <input type="text" name="nom" id="nom" placeholder="Entrez le nom de l'étudiant" required>
                    </div>
                    <div>
                        <label for="prenom">Prénom:</label>
                        <input type="text" name="prenom" id="prenom" placeholder="Entrez le prénom de l'étudiant" required>
                    </div>
                    <div>
                        <label for="dateNaissance">Date de naissance:</label>
                        <input type="date" name="dateNaissance" id="dateNaissance" required>
                    </div>
                    <button type="button" class="submit-button" onclick="createStudent()">Ajouter un étudiant</button>
                </form>
            </div>
            </div>
            <button onclick="loadStudents()" class="reset">Supprimer la recherche</button>
        </div>
        
        <!-- Affichage des données -->
        <table class="donnees">
            <thead>
                <tr>
                    <th>DA</th>
                    <th>Nom</th>
                    <th>Courriel</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="Student-Data">

            </tbody>
        </table>
        <div class="modal-overlay" id="editEtudiantModalOverlay">
            <div class="modal">
                <button class="close-button" id="closeEditEtudiantModal">X</button>
                <h2>Modifier l'étudiant</h2>
                <form method="PUT" action="/api/students">
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
                    <button type="submit" class="submit-button" style="margin-top: 15px;">Mettre à jour l'étudiant</button>
                </form>
            </div>
        </div>
        <!-- pagination -->
        <div class="pagination-nav">
            <form method="POST" action="../Controllers/Controller.php" style="display:inline;">
                <input type="hidden" name="action" value="changementPage">
                <input type="hidden" name="type" value="Etudiants">
                <button class="prev-button" type="submit">Précédent</button>
            </form>
            <div class="page-range">
                Page
            </div>
            <form method="POST" action="../Controllers/Controller.php" style="display:inline;">
                <button class="next-button" type="submit">Suivant</button>
            </form>
        </div>
        <div class="pagination">
        <footer>
            @Copyright gestionCollege 2025
        </footer>
        <script src="/Views/js/modals.js"></script>
    </body>
</html>