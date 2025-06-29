<style>
.menu-toggle {
    position: fixed;
    top: 6px;
    left: 20px;
    z-index: 1000;
    cursor: pointer;
    background: white;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.menu-toggle .bar {
    display: block;
    width: 25px;
    height: 3px;
    background-color: #333;
    margin: 5px 0;
    transition: all 0.3s ease;
}

.menu-toggle.active .bar:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
}

.menu-toggle.active .bar:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active .bar:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
}

.sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 300px;
    height: 100%;
    background-color: white;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: left 0.3s ease;
    z-index: 999;
    overflow-y: auto;
    padding: 20px;
}

.sidebar.active {
    left: 0;
}

.sidebar-header {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
}

.sidebar-categories {
    list-style: none;
    margin-top: 10px;
}

.sidebar-category {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.sidebar-category:hover {
    color: #27ae60;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.overlay.active {
    opacity: 1;
    visibility: visible;
}
</style>

<link rel="stylesheet" href="../css/menu.css">

  
    <div class="menu-toggle" onclick="toggleSidebar()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
        </div>
        <ul class="sidebar-categories">
            <li class="sidebar-category" onclick="window.location.href='home.php'">Home</li>
            <li class="sidebar-category" onclick="window.location.href='progetto.php'">Il Progetto</li>
            <li class="sidebar-category" onclick="window.location.href='collaborazioni.php'">Collaborazioni</li>
            <li class="sidebar-category" onclick="window.location.href='offertePremi.php'">Offerte e premi</li>
        </ul>
    </div>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<script>
    // Funzione per aprire/chiudere il menu laterale
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.querySelector('.menu-toggle');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('active');
        menuToggle.classList.toggle('active');
        overlay.classList.toggle('active');
    }
</script>
