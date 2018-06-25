<!DOCTYPE html>

<html>

<head>
    <title>Angular 4</title>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="http://localhost/App/Views/Header/Styles/header.css">
    <?php if (isset($view_styles)): ?>
    <link type="text/css" rel="stylesheet" href="http://localhost/<?php echo $view_styles ?>">
    <?php endif ?>
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
                <a href="#" class="logo">
                    <img class="svg" src="http://localhost/App/Views/Header/Images/rudev.png">
                    <div class="clear-fix"></div>
                </a>
                <?php if ($user != ""): ?>
                    <div class="navbar-items">
                        <a href="#" class="navbar-item">
                            <div class="navbar-label default-styles">Начало</div>
                        </a>
                        <a href="#angular" class="navbar-item">
                            <div class="navbar-label default-styles">За Angular</div>
                        </a>
                        <a href="#architecture" class="navbar-item">
                            <div class="navbar-label default-styles">Архитектура</div>
                        </a>
                        <a href="#literature" class="navbar-item">
                            <div class="navbar-label default-styles">Литература</div>
                        </a>
                    </div>

                    <div class="navbar-actions">
                        <a href="logout" class="navbar-item">
                            <div class="navbar-label default-styles"><?php echo "Hello, $user" ?></div>
                        </a>
                        <a href="logout" class="navbar-item">
                            <div class="navbar-label default-styles">Logout</div>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="navbar-actions">
                        <a href="login" class="navbar-item">
                            <div class="navbar-label default-styles">Login</div>
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
</body>
</html>