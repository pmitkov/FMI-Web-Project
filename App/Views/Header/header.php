<!DOCTYPE html>

<html>

<head>
    <title>WEB 2018</title>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="http://localhost/App/Views/Header/Styles/header.css">
    <?php if (isset($view_styles)): ?>
    <link type="text/css" rel="stylesheet" href="http://localhost/<?php echo $view_styles ?>">
    <?php endif ?>

    <script src="http://localhost/App/Views/Header/Scripts/smoothie.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        function showModal() {
            var modal = document.getElementById("modal");

            modal.style.display = "flex";
        }

        function hideModal() {
            var modal = document.getElementById("modal");

            modal.style.display = "none";
        }
    </script>
</head>

<body>
<div class="container">
    <div class="backdrop">
        <div class="background-theme">
            <div class="background-header-theme"></div>
        </div>
    </div>
    <div class="navbar">
        <div class="navbar-container">
            <nav class="navbar-desktop">
                <a href="/" class="logo">
                    <img class="svg" src="http://localhost/App/Views/Header/Images/rudev.png">
                    <div class="clear-fix"></div>
                </a>
                <?php if ($user != ""): ?>
                    <div class="navbar-items">
                        <a href="/home" class="navbar-item">
                            <div class="navbar-label default-styles">Home</div>
                        </a>
                        <a href="/charts" class="navbar-item">
                            <div class="navbar-label default-styles">Log Charts</div>
                        </a>
                        <a href="/raw" class="navbar-item">
                            <div class="navbar-label default-styles">Raw Logs</div>
                        </a>
                        <a href="/monitoring" class="navbar-item">
                            <div class="navbar-label default-styles">Monitoring</div>
                        </a>
                    </div>

                    <div class="navbar-actions">
                        <a href="/user-profile" class="navbar-item">
                            <div class="navbar-label default-styles"><?php echo "Hello, $user" ?></div>
                        </a>
                        <a onclick="showModal()" class="navbar-item">
                            <div class="navbar-label default-styles">Logout</div>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="navbar-actions">
                        <a href="/login" class="navbar-item">
                            <div class="navbar-label default-styles">Login</div>
                        </a>
                    </div>
                    <div class="navbar-actions">
                        <a href="/register" class="navbar-item">
                            <div class="navbar-label default-styles">Register</div>
                        </a>
                    </div>
                <?php endif ?>
            </nav>
        </div>
    </div>
    <div class="pane">
        <div class="pane-content">
            <div class="template-container">
                <div class="website-information" id="content">
                    <div class="template">
                        <div class="website-heading">
                            <h1 class="website-headline default-styles">
                                Web Servers live logs
                            </h1>
                        </div>
                        <div class="template-wrapper">
                            <div class="template-content default-styles" id="main-content">
                                <?php if (isset($content)) echo $content ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" onclick="hideModal()">
                    X
                </button>
                <h3 class="modal-title">Logout</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to log out of this account?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" type="button" onclick="hideModal()">Cancel</button>
                <button class="btn btn-danger" type="button"  onclick="location.href='/logout'">Logout</button>
            </div>
        </div>
    </div>
    <div class="modal-backdrop"></div>
</div>
</body>
</html>