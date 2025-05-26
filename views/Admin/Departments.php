<?php
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>

<html>
    <head>
        <link rel="stylesheet" href="Views/General.css">
        <script>
            function showDepartments(data) {
                const tableBody = document.getElementById('Department-Data');
                tableBody.innerHTML = '';
                if (data.page > data.nbPage || data.page < 1) {
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '/departments';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'page';
                    input.value = data.page > data.nbPage ? data.nbPage : 1;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                    return;
                }
                if(data.page >= data.nbPage){
                    document.querySelector('.next-button').disabled = true;
                }else{
                    document.querySelector('.next-button').disabled = false;
                }
                if (data.departments.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="4">Aucunes données disponibles</td></tr>`;
                    return;
                }
                data.departments.forEach(department => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                            <td>${department.nom}</td>
                            <td>${department.code}</td>
                            <td>
                                <button class="open-modal edit image-button" onclick="openEditModal(${department.id}, '${department.nom}', '${department.code}')">
                                    <img src="Views/images/pen.webp" alt="Modifier" class="edit-icon">
                                </button>
                                <button class=\"image-button\" onclick="deleteDepartment(${department.id})">
                                    <img src="Views/images/trash.webp" alt="Supprimer" class="delete-icon">
                                </button>
                            </td>
                        `;
                    tableBody.appendChild(row);
                });
            }
            function loadDepartments(){
                <?php if (isset($_GET['page'])): ?>
                    const page = "<?php echo htmlspecialchars($_GET['page']); ?>";
                <?php else: ?>
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '/departments';

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
                    fetch(`api/departments?query=${encodeURIComponent(query)}&page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        showDepartments(data);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        const tableBody = document.getElementById('Department-Data');
                        tableBody.innerHTML = `<tr><td colspan="4">Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.</td></tr>`;
                    });
                <?php else: ?>
                    fetch(`api/departments?page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        showDepartments(data);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        const tableBody = document.getElementById('Department-Data');
                        tableBody.innerHTML = `<tr><td colspan="4">Une erreur est survenue lors du chargement des données. Veuillez réessayer plus tard.</td></tr>`;
                    });
                <?php endif; ?>
            }

            function createDepartment() {
                const form = document.getElementById('addDepartmentForm');
                const data = {
                    nom: form.nom.value,
                    code: form.code.value,
                    description: form.description.value
                }; 
            
                if (!data.nom || !data.code) {
                    alert("Veuillez remplir tous les champs.");
                    return;
                }
                fetch('api/departments', {
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

            function deleteDepartment(id) {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce département ?')) {
                    fetch(`api/departments/${id}`, {
                        method: 'DELETE',
                    })
                        .then(response => {
                            if (response.ok) {
                                loadDepartments();
                            } else {
                                alert('Une erreur est survenue lors de la suppression du département.');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue. Veuillez réessayer.');
                        });
                }
            }
            // Function to close the "Add Department" modal
            function closeAddModal() {
                const modalOverlay = document.getElementById('modalOverlay');
                modalOverlay.style.display = 'none'; // Hide the modal
            }

            loadDepartments();
        </script>
    </head>

    <body>
        <h1 class="titre">Départements</h1>
        <!-- Options de recherche et d'ajout -->
        <div class="options">
            <form class="recherche" id="searchForm" method="GET" action="/departments">
                <input type="hidden" name="page" value="1">
                <input type="text" id="user_input" name="query" required  value="<?php echo isset($_GET['query']) ? $_GET['query'] : ''; ?>">
                <input class="recherche" type="submit" value="Recherche">
            </form>
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
                    <div>
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" placeholder="Entrez la description du département" required></textarea>
                    </div>
                    <button type="button" class="submit-button" onclick="createDepartment()">Ajouter un département</button>
                </form>
            </div>
            </div>
            <form class="searchForm" method="GET" action="/departments" style="width:200px;margin:none">
                <button type="submit" class="reset">Supprimer la recherche</button>
            </form>
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
        <div class="pagination-nav">
            <form method="GET" action="departments" style="display:inline;">
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
            <form method="GET" action="departments" style="display:inline;">
                <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']+1)?>">
                <button class="next-button" type="submit">Suivant</button>
            </form>
        </div>
        <footer>
            @Copyright gestionCollege 2025
        </footer>
        <script src="Views/js/modals.js"></script>
    </body>

</html>