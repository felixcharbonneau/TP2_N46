<?php
    namespace views\Admin;
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
    $nbPages = 0;
?>
<!-- Page pour afficher la gestion des étudiants pour l'admin -->
<html>
    <head>
        <link rel="stylesheet" href="Views/General.css">
        <script>
            /**
             * Fonction pour afficher les étudiants dans la table
             */
            function showUsers(data) {
                const tableBody = document.getElementById('Student-Data');
                tableBody.innerHTML = '';
                if (data.students.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="4">Aucunes données disponibles</td></tr>`;
                    return;
                }
                data.students.forEach(student => {
                    const row = document.createElement('tr');
                    student.nom = student.nom.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    student.prenom = student.prenom.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    student.da = student.da.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    row.innerHTML = `
                        <td>${student.da}</td>
                        <td>${student.nom} ${student.prenom}</td>
                        <td>${student.email}</td>
                        <td>
                            <button class="open-modal edit image-button" onclick="openEditModal(${student.id}, '${student.nom}', '${student.prenom}', '${student.dateNaissance}')">
                                <img src="views/Images/pen.webp">
                            </button>
                            <button type=\"submit\" class=\"image-button\" onclick=\"deleteStudent(${student.id})\">
                                <img src=\"views/Images/trash.webp\" alt=\"Icon\">
                            </button>
                        </td>`;
                    tableBody.appendChild(row);
                });
                if(data.page >= data.nbPage){
                    document.querySelector('.next-button').disabled = true;
                }else{
                    document.querySelector('.next-button').disabled = false;
                }
            }
            /**
             * Fonction pour charger les étudiants depuis l'API et les afficher dans la table
             */
            function loadStudents(){
                <?php if (isset($_GET['page'])): ?>
                    const page = "<?php echo htmlspecialchars($_GET['page']); ?>";
                <?php else: ?>
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = 'students';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'page';
                    input.value = 1;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                <?php endif; ?>
                <?php if (isset($_GET['query'])): ?>
                    const query = "<?php echo htmlspecialchars($_GET['query']); ?>";
                    fetch(`api/students?query=${encodeURIComponent(query)}&page=${page}`)
                        .then(response => response.json())
                        .then(data => {
                            showUsers(data);
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            const tableBody = document.getElementById('Student-Data');
                            tableBody.innerHTML = `<tr><td colspan="4">Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.</td></tr>`;
                        });
                <?php else: ?>
                    fetch(`api/students?page=${page}`)
                        .then(response => response.json())
                        .then(data => {
                            showUsers(data);
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            const tableBody = document.getElementById('Student-Data');
                            tableBody.innerHTML = `<tr><td colspan="4">Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.</td></tr>`;
                        });
                <?php endif; ?>
                document.getElementById('user_input').value = '';
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
                
                document.getElementById('closeEditStudentModal').addEventListener('click', () => {
                    modal.style.display = 'none';
                });

                window.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }
            function closeEditStudentModal() {
                const modal = document.querySelector('#editEtudiantModalOverlay');
                modal.style.display = 'none';
            }
            /**
             * Fonction pour la suppression d'un étudiant
             * @param {number} studentId - L'ID de l'étudiant à supprimer
             */
            function deleteStudent(studentId) {
                if (!confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?")) {
                    return; // Annule la suppression si l'utilisateur clique sur Annuler
                }
                fetch(`api/students/${studentId}`, {
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
             * Fonction pour mettre à jour un étudiant
             * @param {number} id - L'ID de l'étudiant à mettre à jour
             */
            function updateStudent() {
                const id = document.getElementById('editEtudiantId').value;
                const nom = document.getElementById('editNom').value;
                const prenom = document.getElementById('editPrenom').value;
                const dateNaissance = document.getElementById('editDateNaissance').value;
                const password = document.querySelector('#editEtudiantModalOverlay input[name="password"]').value;

                const data = {
                    nom,
                    prenom,
                    dateNaissance,
                    password
                };
                fetch(`api/students/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (response.status === 204) {
                        loadStudents();
                        closeEditStudentModal();
                        document.getElementById('editNom').value = '';
                        document.getElementById('editPrenom').value = '';
                        document.getElementById('editDateNaissance').value = '';
                        document.querySelector('#editEtudiantModalOverlay input[name="password"]').value = '';
                    } else {
                        alert(response.json());
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
            
                if (!data.nom || !data.prenom || !data.dateNaissance) {
                    alert("Veuillez remplir tous les champs.");
                    return;
                }
                fetch('api/students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                })
                .then(data => {
                    if (data.ok) {
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
        </script>
    </head>
    <body>
        <h1 class="titre">Étudiants</h1>
        <!-- Options de recherche et d'ajout -->
        <div class="options">
            <form class="recherche" id="searchForm" method="GET" action="/students">
                <input type="hidden" name="page" value="1">
                <input type="text" id="user_input" name="query" required  value="<?php echo isset($_GET['query']) ? $_GET['query'] : ''; ?>">
                <input class="recherche" type="submit" value="Recherche">
            </form>
            <!-- modal pour ajouter un étudiant -->
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
            <form class="searchForm" method="GET" action="/students" style="width:200px;margin:none">
                <button type="submit" class="reset">Supprimer la recherche</button>
            </form>
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
            <!-- Modal pour modifier un étudiant -->
            <div class="modal">
                <button class="close-button" id="closeEditStudentModal">X</button>
                <h2>Modifier l'étudiant</h2>
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
        <!-- pagination -->
        <div class="pagination-nav">
            <form method="GET" action="students" style="display:inline;">
            <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']-1)?>">
                <?php if ($_GET['page'] > 1): ?>
                    <button class="prev-button" type="submit">Précédent</button>
                <?php else: ?>
                    <button class="prev-button" type="submit" disabled>Précédent</button>
                <?php endif; ?>
            </form>
            <div class="page-range">
                Page <?php echo htmlspecialchars($_GET['page']); ?>
            </div>
            <form method="GET" action="students" style="display:inline;">
                <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']+1)?>">
                <button class="next-button" type="submit">Suivant</button>
            </form>
        </div>
        <div class="pagination">
        <footer>
            @Copyright gestionCollege 2025
        </footer>
        <script src="Views/js/modals.js"></script>
    </body>
</html>