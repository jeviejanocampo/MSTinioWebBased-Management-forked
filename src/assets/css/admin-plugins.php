<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
<script>
    function loadContent(url, element) {
        $('#contentFrame').attr('src', url);

        $('.nav-link').removeClass('active-link');
        $(element).addClass('active-link');
    }

    function openNav() {
        document.getElementById("mySidebar").style.width = "250px";
        document.getElementById("main").style.marginLeft = "250px";
        localStorage.setItem('sidebarState', 'open');
    }

    function closeNav() {
        document.getElementById("mySidebar").style.width = "0";
        document.getElementById("main").style.marginLeft= "0";
        localStorage.setItem('sidebarState', 'closed'); 
    }

    function checkSidebarState() {
        const state = localStorage.getItem('sidebarState');
        if (state === 'open') {
            openNav();
        } else {
            closeNav(); 
        }
    }
    function loadContent(url, element) {
        // Set the src attribute of the iframe to load the content
        document.getElementById('contentFrame').src = url;

        // Remove the 'active-link' class from all links
        const links = document.querySelectorAll('.sidebar a');
        links.forEach(link => link.classList.remove('active-link'));

        // Add the 'active-link' class to the clicked link
        element.classList.add('active-link');
    }
    window.onload = checkSidebarState;
</script>


<style>
    /* Roboto Regular */
@font-face {
    font-family: 'Roboto';
    src: url('../assets/fonts/Roboto/Roboto-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

/* Roboto Bold */
@font-face {
    font-family: 'Roboto';
    src: url('../assets/fonts/Roboto/Roboto-Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}

@font-face {
    font-family: 'Roboto';
    src: url('../assets/fonts/Roboto/Roboto-Italic.ttf') format('truetype');
    font-weight: normal;
    font-style: italic;
}

@font-face {
    font-family: 'Poppins-lol';
    src: url('../assets/fonts/Poppins/Poppins-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'Poppins';
    src: url('../assets/fonts/Poppins/Poppins-SemiBold.ttf') format('truetype');
    font-weight: 600;
    font-style: normal;
}

/* Poppins Bold */
@font-face {
    font-family: 'Poppins';
    src: url('../assets/fonts/Poppins/Poppins-Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}

body {
    font-family: "Lato", sans-serif;
    background-color: #f8f9fa;
    margin: 0; /* Remove default body margin */
    padding: 0; /* Remove default body padding */
    height: 100vh; /* Ensure body takes full height */
    overflow: hidden; /* Prevent scrolling */
}

.sidebar {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #111;
    overflow-x: hidden;
    transition: 0.5s;
    padding-right: 2px;
}

.sidebar a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    color: #818181;
    display: block;
    transition: 0.3s;
    font-size: 18px;
    margin-bottom: 25px;
    color: white;
}

.sidebar a.active-link {
    background-color: white;
    color: #333;
    border-radius: 5px;
}

.sidebar a:hover {
    color: #f1f1f1;
}

.sidebar .closebtn {
    position: absolute;
    margin-top: 5px;
    top: 0;
    right: 5px;
    font-size: 36px;
    margin-left: 50px;
    font-size: 30px;
    font-weight: 600;
    color: white;
}

.openbtn {
    position: relative;
    right: 30px;
    width: 102%;
    font-size: 20px;
    cursor: pointer;
    background-color: #111;
    color: white;
    padding: 5px 5px;
    margin-left: 5px;
    border: none;
    border-radius: 5px;
    margin-bottom: 0px;
}

.openbtn:hover {
    background-color: #444;
}

#main {
    transition: margin-left .5s;
    padding: 0;
    height: 100vh; 
    display: flex;
    flex-direction: column; 
}

#contentFrame {
    flex: 1; 
    border: none;
    width: 100%;
    height: 100%; 
    margin: 0;
    padding: 0;
}

.profile-section {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #444;
    margin-bottom: 15px;
    background-color: #333;
}

.profile-section img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
}

.profile-section .profile-info {
    color: #fff;
}

.profile-section .profile-info h4 {
    margin: 0;
    font-size: 18px;
}

.profile-section .profile-info p {
    margin: 0;
    font-size: 14px;
}

@media screen and (max-height: 150px) {
    .sidebar {padding-top: 15px;}
    .sidebar a {font-size: 18px;}
}

</style>
