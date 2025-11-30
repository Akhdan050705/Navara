<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Navara Oleh-Oleh</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="welcome-page">

    <div class="welcome-container" id="welcome-container">
        
        <div class="logo">
            <img src="images/logo.png" alt="Navara Oleh-Oleh Logo">
        </div>
        
        <h1 class="welcome-title">Welcome</h1>

        <div id="click-to-start">
            <p>Click anywhere to start</p>
        </div>

        <div id="role-selection" class="hidden">
            <a href="login.php?role=admin" class="btn btn-light">LOGIN AS ADMIN</a>
            <a href="login.php?role=customer" class="btn btn-light">LOGIN AS CUSTOMER</a>
        </div>

    </div>

    <script src="script.js"></script>
</body>
</html>