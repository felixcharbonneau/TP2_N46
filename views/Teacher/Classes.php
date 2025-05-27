<?php require VIEWS_PATH . 'Navbar/TeacherNavbar.php'; ?>

<html>
<head>
    <link rel="stylesheet" href="Views/General.css">
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 100000;
            left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 5px;
            position: relative;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            /* Remove any margin that could offset the box */
            margin: 0;
        }


        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover, .close-modal:focus {
            color: black;
            text-decoration: none;
        }

    </style>
</head>
<body>
<h1 class="titre">Groupes</h1>

<?php if ($classes): ?>
    <?php foreach ($classes as $class): ?>
        <?php
        $nom = htmlspecialchars($class->nom);
        $numero = htmlspecialchars($class->numero);
        $description = htmlspecialchars($class->description);
        $groupStudents = $studentsByGroup[$class->id] ?? [];
        ?>
        <details>
            <summary><strong><?= $nom ?></strong></summary>
            <div style="margin-left: 20px; padding-top: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; max-width: 90%;">
                    <p style="margin: 0;"><strong>Numéro:</strong> <?= $numero ?></p>
                    <button class="add-student-button"
                            data-group-id="<?= htmlspecialchars($class->id) ?>"
                            data-group-name="<?= $nom ?>"
                            title="Ajouter un étudiant à ce groupe"
                            onclick="openModal(this)">
                        +
                    </button>
                </div>
                <p><strong>Description:</strong> <?= $description ?></p>

                <?php if (!empty($groupStudents)): ?>
                    <p><strong>Étudiants dans ce groupe :</strong></p>
                    <ul>
                        <?php foreach ($groupStudents as $student): ?>
                            <li>
                                <?= htmlspecialchars($student['nom'] . ' ' . $student['prenom']) ?>

                                <form method="POST" action="classes/removeStudent" style="display:inline;">
                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['id']) ?>">
                                    <input type="hidden" name="group_id" value="<?= htmlspecialchars($class->id) ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo isset($removeToken) ? htmlspecialchars($removeToken) : ''; ?>">
                                    <button class="image-button" type="submit" onclick="return confirm('Supprimer cet étudiant du groupe ?');">
                                        <img src="views/images/trash.webp" alt="Supprimer" class="delete-icon">
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun étudiant dans ce groupe.</p>
                <?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune classe disponible.</p>
<?php endif; ?>

<!-- Modal overlay -->
<div id="modalOverlay" class="modal-overlay">
    <div id="addStudentModal" class="modal">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Ajouter un étudiant au groupe <span id="modalGroupName"></span></h2>
        <form method="POST" action="classes/addStudent">
            <input type="hidden" name="group_id" id="modalGroupId" value="">
            <input type="hidden" name="csrf_token" value="<?php echo isset($addStudentToken) ? htmlspecialchars($addStudentToken) : ''; ?>">
            <label for="student_id">Sélectionnez un étudiant :</label><br>
            <label for="studentSelect"></label><select name="student_id" id="studentSelect" required>
                <option value="" disabled selected>-- Choisir un étudiant --</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= htmlspecialchars($student->id) ?>">
                        <?= htmlspecialchars($student->nom . ' ' . $student->prenom) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <button type="submit">Ajouter</button>
        </form>
    </div>
</div>

<footer>
    @Copyright gestionCollege 2025
</footer>

<script>
    console.log('script loaded');
    window.openModal = function() { console.log('openModal called'); };
    window.openModal = function(button) {
        const overlay = document.getElementById('modalOverlay');
        const groupId = button.getAttribute('data-group-id');
        const groupName = button.getAttribute('data-group-name');
        document.getElementById('modalGroupId').value = groupId;
        document.getElementById('modalGroupName').textContent = groupName;
        document.getElementById('studentSelect').selectedIndex = 0; // reset select
        overlay.style.display = 'flex'; // show overlay + modal centered
    };

    window.closeModal = function() {
        const overlay = document.getElementById('modalOverlay');
        overlay.style.display = 'none'; // hide overlay + modal
    };

    window.onclick = function(event) {
        const overlay = document.getElementById('modalOverlay');
        if (event.target === overlay) {
            closeModal();
        }
    };

</script>

</body>
</html>
