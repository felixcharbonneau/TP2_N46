<?php
    if (!isset($_GET['page'])) {
        $queryString = isset($_GET['query']) ? "&query=" . urlencode($_GET['query']) : "";
        header("Location: ?page=1" . $queryString);
        exit();
    }
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>
<html>
    <head>

    <link rel="stylesheet" href="Views/General.css">
    <script>
        /**
         * Confirme la suppression d'un cours
         * @returns {boolean} true si l'utilisateur confirme la suppression, false sinon
         */
    function confirmDelete() {
        return confirm("Êtes-vous sûr de vouloir supprimer ce Cours? Cela va supprimer ses groupes correspondant");
    }
    /**
     * Ouvre le modal pour modifier un cours
     * @param {number} courseId id du cours
     * @param {string} courseName nom du cours
     * @param {string} courseNumber numéro du cours
     * @param {string} courseDescription description du cours
     * @param {number} departmentId id du département
     */
    function openEditModal(courseId, courseName, courseNumber, courseDescription, departmentId) {
    const editCourseId = document.getElementById('editCourseId');
    const editNom = document.getElementById('editNom');
    const editNumero = document.getElementById('editNumero');
    const editDescription = document.getElementById('editDescription');
    const editListbox = document.getElementById('editListbox');
    fetch('api/departments')
        .then(response => response.json())
        .then(data => {
            editListbox.innerHTML = '';
            if (data.departments && data.departments.length > 0) {
                data.departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.nom;
                    if (department.id == departmentId) {
                        option.selected = true;
                    }
                    editListbox.appendChild(option);
                });
            } else {
                const noDataOption = document.createElement('option');
                noDataOption.value = '';
                noDataOption.textContent = 'No data available';
                noDataOption.disabled = true;
                editListbox.appendChild(noDataOption);
            }
        })
        .catch(error => {
            console.error('Error loading departments:', error);
            const errorOption = document.createElement('option');
            errorOption.value = '';
            errorOption.textContent = 'Error loading data';
            errorOption.disabled = true;
            editListbox.appendChild(errorOption);
        });

    if (departmentId !== null) {
        for (let i = 0; i < editListbox.options.length; i++) {
            if (editListbox.options[i].value == departmentId) {
                editListbox.options[i].selected = true;
                break;
            }
        }
    } else {
        if (!document.querySelector('#editListbox option[value=""]')) {
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = 'Sélectionnez un département';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            editListbox.add(defaultOption, 0);
        }
    }
    // Set the values in the modal form
    editCourseId.value = courseId;
    editNom.value = courseName;
    editNumero.value = courseNumber;
    editDescription.value = courseDescription;

    for (let i = 0; i < editListbox.options.length; i++) {
        if (editListbox.options[i].value == departmentId) {
            editListbox.options[i].selected = true;
            break;
        }
    }
    const editModalOverlay = document.getElementById('editModalOverlay');
    editModalOverlay.style.display = 'flex';
    editModalOverlay.addEventListener('click', (event) => {
        if (event.target === editModalOverlay) {
            editModalOverlay.style.display = 'none';
        }
    });
    /**
     * Ferme le modal pour modifier un cours lorsqu'on clique sur le bouton de fermeture
     */
    document.getElementById('closeEditModal').addEventListener('click', () => {
        editModalOverlay.style.display = 'none';
    });
}
    </script>
    </head>
    <body>
        <h1 class="titre">Cours</h1>
        <div class="options">
            <form class="recherche" action="/courses" method="GET">
                <input type="hidden" name="type" value="Cours">
                <input type="text" id="user_input" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" required>
                <input class="recherche" type="submit" value="Recherche">
            </form>
            <button class="open-modal ajout">&#x2b;</button>
            <div class="modal-overlay" id="modalOverlay">
            <div class="modal">
                <button class="close-button" id="closeModal">X</button>
                <h2>Ajouter un cours</h2>
                <form method="POST" action="courses/add">
                    <input type="hidden" name="csrf_token" value="<?php echo isset($addToken) ? htmlspecialchars($addToken) : ''; ?>">
                    <input type="hidden" name="query" value="<?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : ''; ?>">
                    <input type="hidden" name="query" value="addCours">
                    <input type="hidden" name="page" value="<?php echo $_GET['page']?>">
                    <input type="hidden" name="id" id="courseId" value="">
                    <div>
                        <label for="nom">Nom du cours:</label>
                        <input type="text" name="nom" placeholder="Entrez le nom du cours" required>
                    </div>
                    <div>
                        <label for="numero">Numéro du cours:</label>
                        <input type="text" name="numero" placeholder="Entrez le numéro du cours" required>
                    </div>
                    <div>
                        <label for="description">Description du cours:</label>
                        <textarea class="description" name="description" placeholder="Description du cours" required></textarea>
                    </div>
                    <div>
                    <label for="listbox">Département:</label>
                    <select name="idDepartement" id="listbox" required>
                        <script>
                            fetch('/api/departments')
                                .then(response => response.json())
                                .then(data => {
                                    const listbox = document.getElementById('listbox');
                                    listbox.innerHTML = '';
                                    if (data.departments && data.departments.length > 0) {
                                        data.departments.forEach(department => {
                                            const option = document.createElement('option');
                                            option.value = department.id;
                                            option.textContent = department.nom;
                                            listbox.appendChild(option);
                                        });
                                    } else {
                                        const noDataOption = document.createElement('option');
                                        noDataOption.value = '';
                                        noDataOption.textContent = 'No data available';
                                        noDataOption.disabled = true;
                                        listbox.appendChild(noDataOption);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading departments:', error);
                                    const errorOption = document.createElement('option');
                                    errorOption.value = '';
                                    errorOption.textContent = 'Error loading data';
                                    errorOption.disabled = true;
                                    document.getElementById('listbox').appendChild(errorOption);
                                });
                        </script>
                    </select>
                    </div>
                    <button type="submit" class="submit-button" style="margin-top: 15px;">Créer un cours</button>
                </form>
            </div>
            </div>
            <form method="GET" action="courses" style="display:inline;">
                <input type="hidden" name="action" value="changementPage">
                <input type="hidden" name="type" value="Cours">
                <input type="submit" class ="reset" value="Supprimer la recherche">
            </form>
        </div>
        <table class="donnees">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (isset($courses) && !empty($courses)) {
                foreach ($courses as $cours) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($cours->numero) . "</td>";
                    echo "<td>" . htmlspecialchars($cours->nom) . "</td>";
                    echo "<td>
                    
                            <button class=\"open-modal edit image-button\" 
                                onclick='openEditModal(" . 
                                htmlspecialchars(json_encode($cours->id), ENT_QUOTES, 'UTF-8') . ", " . 
                                htmlspecialchars(json_encode($cours->nom), ENT_QUOTES, 'UTF-8') . ", " . 
                                htmlspecialchars(json_encode($cours->numero), ENT_QUOTES, 'UTF-8') . ", " . 
                                htmlspecialchars(json_encode($cours->description), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($cours->idDepartement), ENT_QUOTES, 'UTF-8') . ")'>
                                <img src=\"views/Images/pen.webp\">
                            </button>
                        <form class=\"actions\" method=\"POST\" action=\"courses/delete\" onsubmit=\"return confirmDelete();\">
                            <input type=\"hidden\" name=\"page\" value=\"" . htmlspecialchars($_GET['page']) . "\">
                            <input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($cours->id) . "\">
                            <input type=\"hidden\" name=\"csrf_token\" value=\"" . htmlspecialchars($deleteToken) . "\">
                            <input type=\"hidden\" name=\"query\" value=\"" . htmlspecialchars($_POST['query'] ?? '') . "\">
                            <button type=\"submit\" class=\"image-button\">
                                <img src=\"views/Images/trash.webp\" alt=\"Icon\">
                            </button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"3\">Aucune données</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <div class="modal-overlay" id="editModalOverlay">
                <div class="modal">
                    <button class="close-button" id="closeEditModal">X</button>
                    <h2>Modifier le cours</h2>
                    <form method="GET" action="courses/edit">
                        <input type="hidden" name="query" value="<?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : ''; ?>">
                        <input type="hidden" name="page" value="<?php echo $_GET['page']?>">
                        <input type="hidden" name="query" value="editCours">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($editToken) ? htmlspecialchars($editToken) : ''; ?>">
                        <input type="hidden" name="id" id="editCourseId" value="">
                        <div>
                            <label for="editNom">Nom du cours:</label>
                            <input type="text" name="nom" id="editNom" placeholder="Entrez le nom du cours" required>
                        </div>
                        <div>
                            <label for="editNumero">Numéro du cours:</label>
                            <input type="text" name="numero" id="editNumero" placeholder="Entrez le numéro du cours" required>
                        </div>
                        <div>
                            <label for="editDescription">Description du cours:</label>
                            <textarea class="description" name="description" id="editDescription" placeholder="Description du cours" required></textarea>
                        </div>
                        <div>
                            <label for="editListbox">Département:</label>
                            <select name="idDepartement" id="editListbox" required>
                                <?php
                                if (isset($requiredData) && !empty($requiredData)) {
                                    foreach ($requiredData as $item) {
                                        echo "<option value=\"{$item->id}\">{$item->nom}</option>";
                                    }
                                } else {
                                    echo "<option value=\"\" disabled>No data available</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="submit-button" style="margin-top: 15px;">Mettre à jour le cours</button>
                    </form>
                </div>
            </div>
            <div class="pagination-nav">
            <form method="GET" action="courses" style="display:inline;">
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
            <form method="GET" action="courses" style="display:inline;">
                <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                    <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query']); ?>">
                <?php endif; ?>
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']+1)?>">
                <button class="next-button" type="submit">Suivant</button>
            </form>
        </div>
    </div>
        <footer>
            @Copyright gestionCollege 2025
        </footer>
    </body>
    <script src="views/js/Modals.js"></script>
</html>