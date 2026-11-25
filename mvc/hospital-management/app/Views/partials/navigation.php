<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$currentPath = rtrim($currentPath ?: '/', '/');

if ($currentPath === '') {
    $currentPath = '/';
}

$navigationLinks = [
    'Dashboard' => '/',
    'Patients' => '/patients',
    'Doctors' => '/doctors',
    'Appointments' => '/appointments',
    'Prescriptions' => '/prescriptions',
    'Billing' => '/bills'
];
?>
<header class="site-header">
    <a class="site-brand" href="/" aria-label="Hospital Management home">
        <span class="site-brand-mark">H</span>
        <span>Care<span class="site-brand-accent">Point</span></span>
    </a>

    <button
        class="site-menu-toggle"
        type="button"
        aria-label="Toggle navigation menu"
        aria-controls="site-navigation"
        aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <nav id="site-navigation" class="site-nav" aria-label="Main navigation">
        <?php foreach ($navigationLinks as $label => $href): ?>
            <?php
            $isActive = $currentPath === $href ||
                ($href === '/bills' && $currentPath === '/billing');
            ?>
            <a
                class="site-nav-link<?= $isActive ? ' is-active' : '' ?>"
                href="<?= htmlspecialchars(APP_BASE_URL . $href) ?>"
                <?= $isActive ? 'aria-current="page"' : '' ?>>
                <?= htmlspecialchars($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>
<script src="/assets/js/scripts.js" defer></script>
