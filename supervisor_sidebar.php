<div class="sidebar" id="sidebar">
    
    <!-- PROFILE SECTION -->
    <div class="profile">
        <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profile">
        <h3><?= strtoupper(htmlspecialchars($nama_penyelia)) ?></h3>
        <p><?= htmlspecialchars($email) ?></p>
    </div>

    <!-- MENU SECTION -->
    <ul class="sidebar-menu">

        <li>
            <a href="info_list.php" target="contentFrame" data-title="Paparan Info">
                <i class="fas fa-home"></i><span>Paparan Info</span>
            </a>
        </li>

        <li>
            <a href="supervisor_titles.php" target="contentFrame" data-title="Tajuk Penyelia">
                <i class="fas fa-clipboard-list"></i><span>Tajuk Penyelia</span>
            </a>
        </li>

        <li>
            <a href="supervisor_manage_request_permohonan.php" target="contentFrame" data-title="Permohonan Pelajar">
                <i class="fas fa-file-alt"></i><span>Permohonan Pelajar</span>
            </a>
        </li>

        <li>
            <a href="supervisor_projects.php" target="contentFrame" data-title="Pelajar Selian">
                <i class="fas fa-folder-open"></i><span>Pelajar Selian</span>
            </a>
        </li>

        <li>
            <a href="supervisor_temujanji.php" target="contentFrame" data-title="Temujanji">
                <i class="fas fa-calendar-check"></i><span>Temujanji</span>
            </a>
        </li>

        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i><span>Log Keluar</span>
            </a>
        </li>

    </ul>
</div>
