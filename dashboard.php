<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rédaction de Devis - BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="css/custom_style_dashboard.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://fidest.ci/logo/new_logo_banamur.jpg" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php include 'menu.php'; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Rédiger un Devis</h1>

        <!-- Menu Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        <p>Accueil</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="generer_devis.php">
                        <i class="fas fa-file-alt"></i>
                        <p>Générer un devis</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="liste_devis.php">
                        <i class="fas fa-list"></i>
                        <p>Liste des devis</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="liste_facture.php">
                        <i class="fas fa-file-invoice"></i>
                        <p>Liste des factures</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="liste_client.php">
                        <i class="fas fa-users"></i>
                        <p>Liste des clients</p>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="menu-card">
                    <a href="liste_offre.php">
                        <i class="fas fa-gift"></i>
                        <p>Liste des offres</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- ApexCharts Example -->
        <div class="chart-container">
            <h2 class="text-center">Nombre de Devis Édités par Jour</h2>
            <div id="chart"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo gmdate('Y'); ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('request/get_devis_data.php') // Remplacez par le chemin réel
                .then(response => response.json())
                .then(data => {
                    var options = {
                        chart: {
                            type: 'line',
                            height: 350
                        },
                        series: [{
                            name: 'Devis Édités',
                            data: data.data
                        }],
                        xaxis: {
                            categories: data.labels
                        },
                        colors: ['#1d2b57'],
                        stroke: {
                            curve: 'smooth'
                        },
                        title: {
                            text: 'Nombre de Devis Édités par Jour',
                            align: 'left'
                        },
                        grid: {
                            borderColor: '#e0e0e0'
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                });
        });
    </script>

</body>

</html>