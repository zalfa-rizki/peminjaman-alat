<?php $currentPage = $_GET['page'] ?? 'dashboard'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b2b36">
    <title><?= $pageTitle ?? 'Inventory'; ?> - Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="app-body">
<div class="app-shell">
    <aside class="app-sidebar">
        <div class="app-brand"><span class="app-brand-mark"><i class="bi bi-tools"></i></span> Pinjam Alat</div>
        <div class="app-menu-label">Menu</div>
        <nav class="app-nav">
            <a href="index.php?page=dashboard" class="app-nav-link <?= $currentPage==='dashboard'?'active':''; ?>"><i class="bi bi-grid"></i> Dashboard</a>
            <a href="index.php?page=kelola-user" class="app-nav-link <?= $currentPage==='kelola-user'?'active':''; ?>"><i class="bi bi-people"></i> Kelola User</a>
            <a href="index.php?page=kelola-alat" class="app-nav-link <?= $currentPage==='kelola-alat'?'active':''; ?>"><i class="bi bi-tools"></i> Kelola Alat</a>
            <a href="index.php?page=log-aktivitas" class="app-nav-link <?= $currentPage==='log-aktivitas'?'active':''; ?>"><i class="bi bi-clock-history"></i> Log Aktivitas</a>
        </nav>
        <div class="app-sidebar-divider"></div>
        <nav class="app-nav">
            <a href="index.php?page=logout" class="app-nav-link"><i class="bi bi-box-arrow-left"></i> Keluar</a>
        </nav>
    </aside>
    <main class="app-main">
        <div class="app-mobile-top"><strong>Pinjam Alat</strong><a href="index.php?page=logout" class="app-icon-btn" aria-label="Keluar"><i class="bi bi-box-arrow-right"></i></a></div>
        <div class="app-content">
