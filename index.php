<?php
$p = isset($_GET['pagina']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['pagina']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RestaurantePro</title>
        <link rel="icon" type="image/png" href="">

        <!-- Bootstrap e ícones -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

        <?php
        $css_file = "css/" . ($p && file_exists("css/{$p}.css") ? $p : "index") . ".css";
        echo '<link href="' . $css_file . '?' . filemtime($css_file) . '" rel="stylesheet">';
        ?>
    </head>
    <body>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold text-primary" href="?pagina=propaganda">🍽️ RestaurantePro</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav ms-auto d-flex align-items-center gap-2">
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary" href="?pagina=dashboard">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-success" href="?pagina=atendimento">
                                <i class="bi bi-receipt-cutoff"></i> Atendimento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-warning" href="?pagina=produtos">
                                <i class="bi bi-box-seam"></i> Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-info" href="?pagina=clientes">
                                <i class="bi bi-people"></i> Clientes
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



        <!-- Conteúdo Principal -->
        <main class="container">
            <?php
            $html_file = "html/" . ($p && file_exists("html/{$p}.html") ? $p : "index") . ".html";
            include $html_file;
            ?>
        </main>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../library/utils/utils.js?v=<?php echo filemtime('../../library/Ultis/utils.js'); ?>"></script>
        <script src="js/mensagens.js?v=<?php echo filemtime('js/mensagens.js'); ?>"></script>

        <?php
        $js_file = "js/" . ($p && file_exists("js/{$p}.js") ? $p : "index") . ".js";
        echo '<script src="' . $js_file . '?' . filemtime($js_file) . '"></script>';
        ?>

    </body>
</html>
