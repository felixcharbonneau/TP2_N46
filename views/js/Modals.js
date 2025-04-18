document.addEventListener("DOMContentLoaded", function () {
    const openModalButton = document.querySelector('.open-modal');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModalButton = document.getElementById('closeModal');
    const closeEditModalButton = document.getElementById('closeEditDepartmentModal'); 
    /**
     * Ouvre le modal quand le bouton est cliqué
     */
    openModalButton.addEventListener('click', () => {
        modalOverlay.style.display = 'flex'; 
    });
    /**
     * Ferme le modal quand le bouton est cliqué
     */
    closeModalButton.addEventListener('click', () => {
        modalOverlay.style.display = 'none'; 
    });
    /**
     * Ferme le modal quand on clique en dehors du modal
     */
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });
    
    closeEditModalButton.addEventListener('click', function() {
        document.getElementById('editDepartmentModalOverlay').style.display = 'none'; 
    });
});
