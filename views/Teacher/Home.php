<?php
    include VIEWS_PATH . 'Navbar/TeacherNavbar.php';
?>
<html>
    <script>
    let meteoJoliette = [];
        fetch('https://api.open-meteo.com/v1/forecast?latitude=46.0164&longitude=-73.4236&daily=temperature_2m_max,temperature_2m_min&forecast_days=3')
          .then(response => response.json())
          .then(data => {
              meteoJoliette = data;
              document.getElementById("max").innerHTML = meteoJoliette.daily.temperature_2m_max[0] + "°C";
              document.getElementById("min").innerHTML = meteoJoliette.daily.temperature_2m_min[0] + "°C";
          })
          .catch(error => { console.log(error); });
    </script>
    <head>
    <link rel="stylesheet" href="Views/General.css">
    </head>
    <body>
    <h1 class="titre">Acceuil</h1>
        <div id="meteo">
            <h2>Météo de joliette</h2>
            <p>Température maximale: <span id="max"></span></p>
            <p>Température minimale: <span id="min"></span></p>
        </div>
        <footer>
            @Copyright gestionCollege 2025
        </footer>
    </body>
</html>