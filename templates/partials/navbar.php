<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$kullanici_adi_soyadi = $_SESSION['unvan']." ".$_SESSION['ad'];
$kullanici_avatar_url = $_SESSION['avatar'];


?>

<nav class="navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand-custom" href="index.php">
                <img src="assets/images/logo.png" alt="Scholar Mate Logo">
                Scholar Mate
            </a>
            <div class="d-flex align-items-center">
                 <ul class="navbar-nav flex-row me-3">
                    <li class="nav-item"><a class="nav-link-custom" href="index.php">Makalelerim</a></li>
                    <!-- <li class="nav-item"><a class="nav-link-custom ms-2" href="#">Kütüphanem</a></li> -->
                </ul>
                <div class="profile-dropdown dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo "$kullanici_avatar_url"; ?>" alt="Profil Fotoğrafı">
                        <span class="d-none d-sm-inline mx-1" style="color: var(--navbar-link-color);"><?php echo htmlspecialchars($kullanici_adi_soyadi); ?></span>
                    </a>
                     <ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="Profil.php"><i class="bi bi-person-circle"></i> Profilim</a></li>
                        <li><hr class="dropdown-divider" style="border-top: 1px solid var(--input-border);"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Çıkış Yap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>