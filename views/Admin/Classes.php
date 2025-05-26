<?php
    if (!isset($_GET['page'])) {
        $queryString = isset($_GET['query']) ? "&query=" . urlencode($_GET['query']) : "";
        header("Location: ?page=1" . $queryString);
        exit();
    }
    if(isset($_GET['error']) && !empty($_GET['error'])){
        $error = $_GET['error'];
        include_once VIEWS_PATH . 'ErrorMessage.php';
    }
    include VIEWS_PATH . 'Navbar/AdminNavbar.php';
?>


<html>
    <head>
        <script>
            const studentsData = <?php
                $groupStudentMap = [];

                if (!empty($studentsByGroup)) {
                    foreach ($studentsByGroup as $groupId => $students) {
                        $groupStudentMap[$groupId] = array_map(function($student) {
                            return [
                                'prenom' => $student['prenom'],
                                'nom' => $student['nom'],
                                'email' => $student['email'],
                                'id' => $student['id']
                            ];
                        }, $students);
                    }
                }

                echo json_encode($groupStudentMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                ?>;


        </script>

    <link rel="stylesheet" href="Views/General.css">
    <script>
        /**
         * Confirme la suppression d'un Groupe
         * @returns {boolean} true si l'utilisateur confirme la suppression, false sinon
         */
    function confirmDelete() {
        return confirm("Êtes-vous sûr de vouloir supprimer ce Groupe?");
    }
        function openStudentsModal(groupId) {
            const modal = document.getElementById('studentsModalOverlay');
            const list = document.getElementById('studentsList');
            const addButton = document.getElementById('addStudentButton');
            const addStudentModal = document.getElementById('addStudentModal');
            const addStudentForm = document.getElementById('addStudentForm');
            const addStudentGroupId = document.getElementById('addStudentGroupId');

            modal.style.display = 'flex';
            const students = studentsData[groupId]; // Assuming studentsData is globally available.

            addStudentGroupId.value = groupId; // Set group ID for the add student form.

            // Add event listener to open the add student modal when "+" button is clicked
            addButton.onclick = function () {
                openAddStudentModal(groupId);
            };

            // Show the current list of students in the main modal
            if (students && students.length > 0) {
                list.innerHTML = ''; // Clear current list
                students.forEach((student) => {
                    const studentDiv = document.createElement('div');
                    studentDiv.style.display = 'flex';
                    studentDiv.style.justifyContent = 'space-between';
                    studentDiv.style.alignItems = 'center';
                    studentDiv.style.marginBottom = '10px';
                    studentDiv.style.padding = '5px 10px';
                    studentDiv.style.borderBottom = '1px solid #ccc';
                    studentDiv.style.width = '90%';

                    const studentText = document.createElement('div');
                    studentText.textContent = `Nom: ${student.prenom} ${student.nom}, Email: ${student.email}`;
                    studentText.style.flex = '1';

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = "-";
                    deleteButton.style.fontSize = "20px";
                    deleteButton.style.marginLeft = '10px';
                    deleteButton.onclick = function () {
                        if (confirm("Êtes-vous sûr de vouloir enlever cet étudiant du groupe?")) {
                            // Create and submit a form to remove the student
                            removeStudent(student.id, groupId);
                        }
                    };

                    studentDiv.appendChild(studentText);
                    studentDiv.appendChild(deleteButton);
                    list.appendChild(studentDiv);
                });
            } else {
                list.innerHTML = '<p>Aucun étudiant trouvé dans ce groupe.</p>';
            }

            // Close the main modal logic
            document.getElementById('closeStudentsModal').onclick = () => modal.style.display = 'none';
            modal.onclick = e => {
                if (e.target === modal) modal.style.display = 'none';
            };

            // Function to open the Add Student Modal
            function openAddStudentModal(groupId) {
                addStudentModal.style.display = 'flex';



                // Close the Add Student Modal logic
                document.getElementById('closeAddStudentModal').onclick = () => addStudentModal.style.display = 'none';
                addStudentModal.onclick = e => {
                    if (e.target === addStudentModal) addStudentModal.style.display = 'none';
                };
            }
        }

        // Function to remove a student
        function removeStudent(studentId, groupId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/classes/removeStudent';

            const inputs = [
                { name: 'csrf_token', value: '<?php echo isset($removeToken) ? htmlspecialchars($removeToken) : ""; ?>' },
                { name: 'student_id', value: studentId },
                { name: 'group_id', value: groupId }
            ];

            inputs.forEach(inputData => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputData.name;
                input.value = inputData.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }





        /**
     * Ouvre la modale d'édition d'un Groupe
     * @param {number} classeId - L'ID du Groupe à modifier
     *  @param {string} classeName - Le nom du Groupe
     * @param {string} classeNumber - Le numéro du Groupe
     * @param {string} classeDescription - La description du Groupe
     * @param {number} coursId - L'ID du Cours
     * @param {number} enseignantId - L'ID de l'Enseignant
     * @returns {void}
     */
function openEditModal(classeId, classeName, classeNumber, classeDescription, coursId, enseignantId) {
    document.getElementById('editclasseId').value = classeId;
    document.getElementById('editNom').value = classeName;
    document.getElementById('editNumero').value = classeNumber;
    document.getElementById('editDescription').value = classeDescription;

    const coursSelect = document.getElementById('editCoursId');
    if (coursId) {
        coursSelect.value = coursId;
    } else {
        // sélectionne la première option désactivée "Aucun cours"
        coursSelect.selectedIndex = 0;
    }

    const enseignantSelect = document.getElementById('editEnseignantId');
    if (enseignantId) {
        enseignantSelect.value = enseignantId;
    } else {
        // sélectionne la première option désactivée "Aucun enseignant"
        enseignantSelect.selectedIndex = 0;
    }

    const editModalOverlay = document.getElementById('editModalOverlay');
    editModalOverlay.style.display = 'flex';

    editModalOverlay.addEventListener('click', (event) => {
        if (event.target === editModalOverlay) {
            editModalOverlay.style.display = 'none';
        }
    });

    document.getElementById('closeEditModal').addEventListener('click', () => {
        editModalOverlay.style.display = 'none';
    });
}

    </script>
    </head>
    <body>
        <h1 class="titre">Groupe</h1>
        <div class="options">
            <form class="recherche" action="/classes" method="GET">
                <input type="hidden" name="type" value="Groupe">
                <input type="text" id="user_input" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" required>
                <input class="recherche" type="submit" value="Recherche">
            </form>
<button class="open-modal ajout">&#x2b;</button>
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <button class="close-button" id="closeModal">X</button>
        <h2>Ajouter un Groupe</h2>
        <form method="POST" action="/classes/add">
            <input type="hidden" name="csrf_token" value="<?php echo isset($addToken) ? htmlspecialchars($addToken) : ''; ?>">
            <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? (int)$_GET['page'] : 1; ?>">
            <input type="hidden" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">

            <div>
                <label for="addNom">Nom du Groupe:</label>
                <input type="text" name="nom" id="addNom" placeholder="Entrez le nom du Groupe" required>
            </div>
            <div>
                <label for="addNumero">Numéro du Groupe:</label>
                <input type="text" name="numero" id="addNumero" placeholder="Entrez le numéro du Groupe" required>
            </div>
            <div>
                <label for="addCoursId">Cours:</label>
                <select name="coursId" id="addCoursId" required>
                    <?php if (!isset($classCoursId) || empty($classCoursId)): ?>
                        <option value="" disabled selected>Aucun cours</option>
                    <?php else: ?>
                        <option value="" disabled>Aucun cours</option>
                    <?php endif; ?>
                    <?php
                    if (isset($courses) && !empty($courses)) {
                        foreach ($courses as $cours) {
                            echo '<option value="' . htmlspecialchars($cours->id) . '">' . htmlspecialchars($cours->nom) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="addEnseignantId">Enseignant:</label>
                <select name="idEnseignant" id="addEnseignantId" required>
                    <?php if (!isset($classEnseignantId) || empty($classEnseignantId)): ?>
                        <option value="" disabled selected>Aucun enseignant</option>
                    <?php else: ?>
                        <option value="" disabled>Aucun enseignant</option>
                    <?php endif; ?>
                    <?php
                    if (isset($teachers) && !empty($teachers)) {
                        foreach ($teachers as $teacher) {
                            echo '<option value="' . htmlspecialchars($teacher->id) . '">' . htmlspecialchars($teacher->prenom . ' ' . $teacher->nom) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="addDescription">Description du Groupe:</label>
                <textarea class="description" name="description" id="addDescription" placeholder="Description du Groupe" required></textarea>
            </div>
            <button type="submit" class="submit-button" style="margin-top: 15px;">Ajouter un groupe</button>
        </form>
    </div>
</div>


            <form method="GET" action="classes" style="display:inline;">
                <input type="hidden" name="action" value="changementPage">
                <input type="hidden" name="type" value="Groupe">
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
            if (isset($classes) && !empty($classes)) {
                foreach ($classes as $class) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($class->numero) . "</td>";
                    echo "<td>" . htmlspecialchars($class->nom) . "</td>";
                    echo "<td>
                    
                            <button class=\"open-modal edit image-button\" 
                                onclick='openEditModal(" .
                                htmlspecialchars(json_encode($class->id), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($class->nom), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($class->numero), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($class->description), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($class->coursID), ENT_QUOTES, 'UTF-8') . ", " .
                                htmlspecialchars(json_encode($class->enseignantID), ENT_QUOTES, 'UTF-8') .
                            ")'>                                <img src=\"views/Images/pen.webp\">

<button type=\"button\" class=\"image-button\" onclick=\"openStudentsModal(" . htmlspecialchars(json_encode($class->id), ENT_QUOTES, 'UTF-8') . ")\">
    <img src=\"Views/Images/eleve.png\" alt=\"Voir les étudiants\">
</button>
                        <form class=\"actions\" method=\"POST\" action=\"classes/delete\" onsubmit=\"return confirmDelete();\">
                            <input type=\"hidden\" name=\"page\" value=\"" . htmlspecialchars($_GET['page']) . "\">
                            <input type=\"hidden\" name=\"query\" value=\"" . (isset($_GET['query']) ? htmlspecialchars($_GET['query']) : 'delete') . "\">
                            <input type=\"hidden\" name=\"id\" value=\"" . htmlspecialchars($class->id) . "\">
                            <input type=\"hidden\" name=\"csrf_token\" value=\"" . htmlspecialchars($deleteToken) . "\">
                            <input type=\"hidden\" name=\"query\" value=\"" . htmlspecialchars($_POST['query'] ?? '') . "\">
                            <button type=\"submit\" class=\"image-button\">
                                <img src=\"Views/Images/trash.webp\" alt=\"Icon\">
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
        <!-- Main Students Modal -->
        <div class="modal-overlay" id="studentsModalOverlay" style="display:none;">
            <div class="modal">
                <button class="close-button" id="closeStudentsModal">X</button>

                <div style="display: flex; justify-content: center; align-items: center; width: 100%; margin-bottom: 20px;">
                    <h2 style="margin-right: 10px;">Étudiants du Groupe</h2>
                    <button id="addStudentButton" style="font-size: 20px; padding: 5px 10px;">+</button>
                </div>

                <div style="overflow: scroll; height: 500px; width: 100%" id="studentsList">
                    <p>Chargement...</p>
                </div>
            </div>
        </div>

        <!-- Add Student Modal (second modal) -->
        <div class="modal-overlay" id="addStudentModal" style="display:none;">
            <div class="modal">
                <button class="close-button" id="closeAddStudentModal">X</button>
                <h2>Ajouter un étudiant au groupe</h2>
                <form id="addStudentForm" method="POST" action="/classes/addStudent">
                    <input type="hidden" name="group_id" id="addStudentGroupId" value="">
                    <input type="hidden" name="csrf_token" value="<?php echo isset($addStudentToken) ? htmlspecialchars($addStudentToken) : ''; ?>">
                    <div>
                        <label for="addStudentId">Sélectionner un étudiant:</label>
                        <select name="student_id" id="addStudentId" required>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo $student['prenom'] . ' ' . $student['nom']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="submit-button" style="margin-top: 15px;">Ajouter l'étudiant</button>
                </form>
            </div>
        </div>






        <div class="modal-overlay" id="editModalOverlay">
<div class="modal">
    <button class="close-button" id="closeEditModal">X</button>
    <h2>Modifier le Groupe</h2>
    <form method="POST" action="classes/edit">
        <input type="hidden" name="query" value="<?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : ''; ?>">
        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo isset($editToken) ? htmlspecialchars($editToken) : ''; ?>">
        <input type="hidden" name="id" id="editclasseId" value="">

        <!-- Nom du Groupe -->
        <div>
            <label for="editNom">Nom du Groupe:</label>
            <input type="text" name="nom" id="editNom" placeholder="Entrez le nom du Groupe" required>
        </div>

        <!-- Numéro du Groupe -->
        <div>
            <label for="editNumero">Numéro du Groupe:</label>
            <input type="text" name="numero" id="editNumero" placeholder="Entrez le numéro du Groupe" required>
        </div>

        <!-- Cours -->
        <div>
            <label for="editCoursId">Cours:</label>
            <select name="coursId" id="editCoursId" required>
                <?php if (!isset($classCoursId) || empty($classCoursId)): ?>
                    <option value="" disabled selected>Aucun cours</option>
                <?php else: ?>
                    <option value="" disabled>Aucun cours</option>
                <?php endif; ?>

                <?php
                if (isset($courses) && !empty($courses)) {
                    foreach ($courses as $cours) {
                        echo '<option value="' . htmlspecialchars($cours->id) . '">' . htmlspecialchars($cours->nom) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <!-- Enseignant -->
        <div>
            <label for="editEnseignantId">Enseignant:</label>
            <select name="idEnseignant" id="editEnseignantId" required>
                <?php if (!isset($classEnseignantId) || empty($classEnseignantId)): ?>
                    <option value="" disabled selected>Aucun enseignant</option>
                <?php else: ?>
                    <option value="" disabled>Aucun enseignant</option>
                <?php endif; ?>

                <?php
                if (isset($teachers) && !empty($teachers)) {
                    foreach ($teachers as $teacher) {
                        echo '<option value="' . htmlspecialchars($teacher->id) . '">' . htmlspecialchars($teacher->prenom . ' ' . $teacher->nom) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <!-- Description du Groupe -->
        <div>
            <label for="editDescription">Description du Groupe:</label>
            <textarea class="description" name="description" id="editDescription" placeholder="Description du Groupe" required></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-button" style="margin-top: 15px;">Mettre à jour le Groupe</button>
    </form>
</div>

            </div>
            <div class="pagination-nav">
            <form method="GET" action="classes" style="display:inline;">
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
            <form method="GET" action="classes" style="display:inline;">
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