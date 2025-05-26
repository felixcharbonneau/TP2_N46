<?php
    if (!isset($_GET['page'])) {
        $queryString = isset($_GET['query']) ? "&query=" . urlencode($_GET['query']) : "";
        header("Location: ?page=1" . $queryString);
        exit();
    }
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>
<!-- Page pour afficher la gestion des Enseignants pour l'admin -->
<html>
    <head>
        <link rel="stylesheet" href="/Views/General.css">
        <script>

            // Fetch departments from the API
            function fetchDepartments() {
                fetch('api/departments')
                    .then(response => response.json())
                    .then(departments => {
                        populateDepartmentSelect(departments);
                    })
                    .catch(error => {
                        console.error('Error fetching departments:', error);
                        alert("Impossible de charger les départements.");
                    });
            }
// Function to handle teacher deletion
function deleteTeacher(teacherId) {
    // Ask for confirmation before deleting
    if (confirm("Êtes-vous sûr de vouloir supprimer cet enseignant ?")) {	
        // Send DELETE request to API
        fetch(`api/teachers/${teacherId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                // Add authentication headers if needed
                // 'Authorization': 'Bearer <token>'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete teacher');
            }
            // If the deletion was successful, remove the teacher from the table
            document.querySelector(`button[onclick="deleteTeacher(${teacherId})"]`).closest('tr').remove();
        })
        .catch(error => {
            console.error("Error deleting teacher:", error);
            alert("There was an error deleting the teacher. Please try again.");
        });
    }
}

// Populate the department <select> dropdown
function populateDepartmentSelect(departments) {
    const departmentSelects = document.querySelectorAll('select[name="departement"], select[name="editDepartement"]'); // For both add and edit modals
    
    departmentSelects.forEach(select => {
        select.innerHTML = ''; // Clear existing options

        // Add a default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Sélectionner un département';
        defaultOption.disabled = true;
        select.appendChild(defaultOption);
        // Add options for each department
        departments.departments.forEach(department => {
            const option = document.createElement('option');
            option.value = department.id;
            option.textContent = department.nom;
            select.appendChild(option);
        });
    });
}

            /*
             * Fonction pour afficher les enseignants dans le tableau
             * @param {Object} data - Les données des enseignants à afficher
             */
function showUsers(data) {
    const tableBody = document.getElementById('teacher-Data');
    tableBody.innerHTML = '';

    // Check if there are teachers in the data
    if (data.teachers.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="4">Aucune donnée disponible</td></tr>`;
        return;
    }

    // Loop through the teachers and create rows
data.teachers.forEach(teacher => {
    const row = document.createElement('tr');

    // Escaping any special characters to avoid XSS attacks
    teacher.nom = teacher.nom.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    teacher.prenom = teacher.prenom.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    teacher.email = teacher.email.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    teacher.idDepartement = teacher.idDepartement == null ? -1 : teacher.idDepartement;

    // Add teacher data to the row
    row.innerHTML = `
        <td>${teacher.nom} ${teacher.prenom}</td>
        <td>${teacher.email}</td>
        <td>
            <button class="open-modal edit image-button" onclick="openEditModal(${teacher.id}, '${teacher.nom}', '${teacher.prenom}', '${teacher.dateNaissance}', ${teacher.idDepartement})">
                <img src="Views/Images/pen.webp" alt="Edit">
            </button>
            <button type="submit" class="image-button" onclick="deleteTeacher(${teacher.id})">
                <img src="Views/Images/trash.webp" alt="Delete">
            </button>
        </td>`;

    tableBody.appendChild(row);
});


    // Handle pagination buttons if needed
    const paginationContainer = document.getElementById('pagination-container');
    const nextButton = document.querySelector('.next-button');
    const prevButton = document.querySelector('.prev-button');
    const pageInfo = document.getElementById('page-info');

    if (data.page && data.nbPage) {
        // Update page info (e.g., Page 1 of 5)
        pageInfo.innerHTML = `Page ${data.page} of ${data.nbPage}`;

        // Disable next button if we're on the last page
        if (data.page >= data.nbPage) {
            nextButton.disabled = true;
        } else {
            nextButton.disabled = false;
        }

        // Disable previous button if we're on the first page
        if (data.page <= 1) {
            prevButton.disabled = true;
        } else {
            prevButton.disabled = false;
        }
    } else {
        // If no pagination data is available, hide pagination container
        paginationContainer.style.display = 'none';
    }
}

            function loadteachers() {
                const page = "<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>"; // Récupérer le numéro de page
                const query = "<?php echo isset($_GET['query']) ? $_GET['query'] : ''; ?>"; // Récupérer la requête de recherche
            
                let url = `api/teachers?page=${page}`; // URL de base pour récupérer les enseignants
                if (query) {
                    url += `&query=${encodeURIComponent(query)}`; // Ajouter la requête de recherche à l'URL
                }
            
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.page >= data.nbPage){
                            document.querySelector(".next-button").disabled = true;
                        }
                        if (data && data.teachers) {
                            showUsers(data);  // If the data has 'teachers', pass it to showUsers
                        } else {
                            console.error("Erreur dans la structure des données: 'teachers' est manquant");
                        }
                    })
            }
        
            /**
             * Fonction pour ouvrir le modal d'édition d'un Enseignant
             * @param {number} id - L'ID de l'Enseignant à éditer
             * @param {string} nom - Le nom de l'Enseignant
             * @param {string} prenom - Le prénom de l'Enseignant
             * @param {string} dateNaissance - La date de naissance de l'Enseignant
             */
function openEditModal(id, nom, prenom, dateNaissance, departmentId) {
    const editEtudiantId = document.getElementById('editEtudiantId');
    const editNom = document.getElementById('editNom');
    const editPrenom = document.getElementById('editPrenom');
    const editNaissance = document.getElementById('editDateNaissance');
    const editDepartement = document.getElementById('editDepartement'); // The department select input

    // Populate the fields with the teacher data
    editEtudiantId.value = id;
    editNom.value = nom;
    editPrenom.value = prenom;
    const formattedDate = new Date(dateNaissance).toISOString().split('T')[0];
    editNaissance.value = formattedDate;

    // Set the selected department
    if (editDepartement) {
        // Loop through the options and set the selected one
        const departmentSelects = editDepartement.querySelectorAll('option');
        departmentSelects.forEach(option => {
            if (option.value == departmentId) {
                option.selected = true;
            }
        });
    }

    const modal = document.querySelector('#editEtudiantModalOverlay');
    modal.style.display = 'block';

    // Close modal when clicking the close button
    document.getElementById('closeEditteacherModal').addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal when clicking outside of the modal
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

            function closeEditteacherModal() {
                const modal = document.querySelector('#editEtudiantModalOverlay');
                modal.style.display = 'none';
            }
            /**
             * Fonction pour la suppression d'un Enseignant
             * @param {number} teacherId - L'ID de l'Enseignant à supprimer
             */
            function deleteteacher(teacherId) {
                fetch(`api/teachers/${teacherId}`, {
                    method: 'DELETE'
                })
                .then(async response => {
                    if (response.status === 204) {
                        loadteachers();
                    } else {
                        let errorMsg = 'Erreur lors de la suppression';
                        throw new Error(errorMsg);
                    }
                })
                .catch(error => alert(error.message));
            }
            /**
             * Fonction pour mettre à jour un Enseignant
             * @param {number} id - L'ID de l'Enseignant à mettre à jour
             */
           function updateteacher() {
                const id = document.getElementById('editEtudiantId').value;
                const nom = document.getElementById('editNom').value;
                const prenom = document.getElementById('editPrenom').value;
                const dateNaissance = document.getElementById('editDateNaissance').value;
                let password = null;  // Default value for password

                // Check if the password input exists
                const passwordInput = document.getElementById("editPassword");
                if (passwordInput) {
                    password = passwordInput.value.trim() === "" ? null : passwordInput.value;
                }

                const data = {
                    nom,
                    prenom,
                    dateNaissance,
                    departement,
                    password
                };

                // Update the teacher information via API
                fetch(`api/teachers/${id}`, {
                    method: 'PUT',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                }).then(response => {
                    if (response.status === 204) {
                        // No content, so just resolve with empty object or whatever you prefer
                        return {};
                    }
                    // Otherwise try to parse JSON
                    return response.json();
                }).then(data => {
                    // Handle success — if API uses 204 No Content, data is {}
                    loadteachers();
                    closeEditteacherModal();
                }).catch(error => {
                    alert('Error: ' + error.message);
                });
            }


            /**
             * Fonction pour créer un nouvel Enseignant
             */
            function createteacher() {
                const form = document.getElementById('addteacherForm');
                const data = {
                    nom: form.nom.value,
                    prenom: form.prenom.value,
                    dateNaissance: form.dateNaissance.value,
                }; 
            
                if (!data.nom || !data.prenom || !data.dateNaissance) {
                    alert("Veuillez remplir tous les champs.");
                    return;
                }
                fetch('api/teachers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                })
                    .then(response => {
                        if (response.ok) {
                            // Success, no content
                            loadteachers();
                            closeAddModal();
                        } else if (response.status === 400) {
                            alert('Données invalides');
                        } else {
                            alert('Une erreur est survenue lors de la création de l\'Enseignant.');
                        }
                    })
                    .catch(error => alert(error.message));

            }
            function closeAddModal() {
                const modal = document.querySelector('#modalOverlay');
                modal.style.display = 'none';
            }
            
            loadteachers();
            fetchDepartments(); // Fetch departments when the page loads
        </script>
    </head>
    <body>
        <h1 class="titre">Enseignants</h1>
        <!-- Options de recherche et d'ajout -->
        <div class="options">
            <form class="recherche" id="searchForm" method="GET" action="/teachers">
                <input type="hidden" name="page" value="1">
                <input type="text" id="user_input" name="query" required  value="<?php echo isset($_GET['query']) ? $_GET['query'] : ''; ?>">
                <input class="recherche" type="submit" value="Recherche">
            </form>
            <!-- modal pour ajouter un Enseignant -->
            <button class="open-modal ajout">&#x2b;</button>
           <!-- Modal for adding a teacher -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <button class="close-button" id="closeModal">X</button>
        <h2>Ajouter un Enseignant</h2>
        <form method="POST" action="api/teachers" id="addteacherForm">
            <div>
                <label for="nom">Nom:</label>
                <input type="text" name="nom" id="nom" placeholder="Entrez le nom de l'Enseignant" required>
            </div>
            <div>
                <label for="prenom">Prénom:</label>
                <input type="text" name="prenom" id="prenom" placeholder="Entrez le prénom de l'Enseignant" required>
            </div>
            <div>
                <label for="dateNaissance">Date de naissance:</label>
                <input type="date" name="dateNaissance" id="dateNaissance" required>
            </div>
            <!-- Department selection -->
            <div>
                <label for="departement">Département:</label>
                <select name="departement" id="departement" required>
                </select>
            </div>
            <button type="button" class="submit-button" onclick="createteacher()">Ajouter un Enseignant</button>
        </form>
    </div>

            </div>
            <form class="searchForm" method="GET" action="/teachers" style="width:200px;margin:none">
                <button type="submit" class="reset">Supprimer la recherche</button>
            </form>
        </div>
        
        <!-- Affichage des données -->
        <table class="donnees">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Courriel</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="teacher-Data">
            </tbody>
        </table>
        <div class="modal-overlay" id="editEtudiantModalOverlay">
            <!-- Modal pour modifier un Enseignant -->
            <div class="modal">
                <button class="close-button" id="closeEditteacherModal">X</button>
                <h2>Modifier l'Enseignant</h2>
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
                <label for="editDepartement">Département:</label>
                <select name="departement" id="editDepartement" required>
                     <option value="-1" disabled selected>Select a Department</option>
                </select>
            </div>
                    <div>
                        <label for="editpassword">Mot de passe:</label>
                        <input type="text" id="editPassword" name="password">
                    </div>
                    <button onclick="updateteacher()" class="submit-button" style="margin-top: 15px;">Mettre à jour l'Enseignant</button>
                </div>
            </div>
        </div>
        <!-- pagination -->
        <div class="pagination-nav">
            <form method="GET" action="teachers" style="display:inline;">
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
            <form method="GET" action="teachers" style="display:inline;">
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