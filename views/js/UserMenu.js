/**
 * Fonction pour afficher le menu glissant de l'utilisateur
 */
document.addEventListener('DOMContentLoaded', function () {
    const icon = document.getElementById('icon');
    const slidingDiv = document.getElementById('slidingDiv');

    icon.addEventListener('click', function (event) {
        event.stopPropagation();
        slidingDiv.classList.toggle('show');
        slidingDiv.classList.toggle('hidden');
    });

    document.addEventListener('click', function (event) {
        if (!slidingDiv.contains(event.target) && !icon.contains(event.target)) {
            slidingDiv.classList.add('hidden');
            slidingDiv.classList.remove('show');
        }
    });
});