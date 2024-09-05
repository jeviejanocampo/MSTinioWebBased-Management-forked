<head>
    <?php include './src/assets/css/admin-plugins.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Administrator Dashboard</title>
</head>
<body>
    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">◀</a>
        <div class="profile-section">
        <img src="./src/assets/images/pepo.jpg" alt="Profile Picture">
        <div class="profile-info">
                <h4>Jevie Jan A. Ocampo</h4>
                <p>Administrator</p>
            </div>
        </div>
        <a href="#" onclick="loadContent('src/layouts/dashboard.php', this)">
            <i class="fas fa-file mr-2"></i> Dashboard
        </a>
        <a href="#" onclick="loadContent('src/layouts/client.php', this)">
            <i class="fas fa-file mr-2"></i> Client
        </a>
        <a href="#" onclick="loadContent('src/layouts/dashboard.php', this)">
            <i class="fas fa-file mr-2"></i> Dashboard
        </a>
        <a href="#" onclick="loadContent('src/pages/client.php', this)">
            <i class="fas fa-file mr-2"></i> Client
        </a> <a href="#" onclick="loadContent('src/layouts/dashboard.php', this)">
            <i class="fas fa-file mr-2"></i> Dashboard
        </a>
        <a href="#" onclick="loadContent('src/pages/client.php', this)">
            <i class="fas fa-file mr-2"></i> Client
        </a>
        <a href="src/pages/login-page.php">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>

    <div id="main">
        <button class="openbtn" onclick="openNav()">☰</button>
        <iframe id="contentFrame" src="src/layouts/dashboard.php"></iframe>
    </div>
</body>
