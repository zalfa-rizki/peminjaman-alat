<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b2b36">
    <title>Login - Sistem Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root{--ink:#0b2b36;--brand:#0f8b8d;--brand-dark:#0a6c6e;--brand-soft:#e3f4f4;--amber:#ffb703;--line:#e1e9ec;--muted:#5f7480}
        *{box-sizing:border-box}
        body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;margin:0;min-height:100vh;background:#f2f6f7;color:#12262e}
        .auth{display:grid;grid-template-columns:1.1fr 1fr;min-height:100vh}
        .auth-hero{background:linear-gradient(160deg,#0b2b36 0%,#0f4a57 100%);color:#fff;padding:56px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden}
        .auth-hero::before,.auth-hero::after{content:"";position:absolute;border-radius:50%;border:1.5px solid rgba(255,255,255,.12)}
        .auth-hero::before{width:520px;height:520px;right:-170px;top:-150px}
        .auth-hero::after{width:320px;height:320px;right:-60px;top:-50px;border-color:rgba(255,183,3,.35)}
        .hero-brand{display:flex;align-items:center;gap:12px;font-weight:800;font-size:18px;position:relative}
        .hero-mark{width:40px;height:40px;border-radius:12px;background:var(--amber);color:var(--ink);display:grid;place-items:center;font-size:20px;transform:rotate(-6deg)}
        .hero-title{font-size:clamp(34px,4.2vw,54px);font-weight:800;letter-spacing:-1.8px;line-height:1.05;margin:0 0 18px;max-width:520px;position:relative}
        .hero-sub{color:#b7d0d6;font-size:16px;line-height:1.6;max-width:430px;margin:0 0 34px;position:relative}
        .flow{display:flex;gap:10px;flex-wrap:wrap;position:relative}
        .flow span{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.16);padding:9px 14px;border-radius:100px;font-size:13px;font-weight:600}
        .flow i{color:var(--amber)}
        .auth-form{display:flex;align-items:center;justify-content:center;padding:32px}
        .auth-box{width:100%;max-width:400px}
        .auth-box h1{font-size:28px;font-weight:800;letter-spacing:-.8px;margin:0 0 6px}
        .auth-box .lead2{color:var(--muted);font-size:14px;margin:0 0 26px}
        .nav-pills{background:#e6eef0;border-radius:14px;padding:4px}
        .nav-pills .nav-link{border-radius:11px;font-size:14px;font-weight:700;color:var(--muted);padding:10px}
        .nav-pills .nav-link.active{background:#fff;color:var(--ink);box-shadow:0 2px 8px rgba(11,43,54,.12)}
        .form-label{font-size:13px;font-weight:700;margin-bottom:7px}
        .field{position:relative}
        .field>i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#93a5ae}
        .field .form-control{padding-left:42px}
        .form-control{background:#fff;border:1px solid var(--line);border-radius:12px;padding:13px 14px;font-size:14px}
        .form-control:focus{border-color:var(--brand);box-shadow:0 0 0 4px var(--brand-soft)}
        .eye{position:absolute;right:6px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#93a5ae;width:36px;height:36px;border-radius:10px}
        .eye:hover{color:var(--ink);background:#f2f6f7}
        .btn-main{background:var(--brand);border:0;color:#fff;border-radius:12px;padding:13px;font-weight:700;width:100%;transition:.15s}
        .btn-main:hover{background:var(--brand-dark);transform:translateY(-1px);box-shadow:0 10px 20px -8px rgba(15,139,141,.6)}
        .btn-alt{background:#fff;color:var(--ink);border:1.5px solid var(--ink);border-radius:12px;padding:13px;font-weight:700;width:100%}
        .btn-alt:hover{background:var(--ink);color:#fff}
        .alert{border-radius:12px;font-size:13px}
        :focus-visible{outline:3px solid var(--amber);outline-offset:2px}
        @media(max-width:860px){.auth-hero{min-height:0}.form-control{font-size:16px}.hero-brand{margin-bottom:18px}.auth{grid-template-columns:1fr}.auth-hero{padding:28px 24px 32px}.hero-title{font-size:30px;margin-bottom:10px}.hero-sub,.flow{display:none}.auth-form{padding:28px 20px 40px}}
    </style>
</head>
<body>
<div class="auth">
    <section class="auth-hero">
        <div class="hero-brand"><span class="hero-mark"><i class="bi bi-tools"></i></span> Pinjam Alat</div>
        <div>
            <h2 class="hero-title">Pinjam alat, tanpa ribet.</h2>
            <p class="hero-sub">Ajukan peminjaman, pantau persetujuan, dan kembalikan alat. Semua tercatat rapi dalam satu sistem.</p>
            <div class="flow"><span><i class="bi bi-send"></i> Ajukan</span><span><i class="bi bi-patch-check"></i> Disetujui petugas</span><span><i class="bi bi-box-arrow-in-down"></i> Dikembalikan</span></div>
        </div>
        <div style="color:#7fa3ad;font-size:12px;position:relative">© 2026 Pinjam Alat</div>
    </section>
    <section class="auth-form"><div class="auth-box">
        <h1>Selamat datang</h1>
        <p class="lead2">Masuk untuk mulai meminjam atau mengelola alat.</p>
        <ul class="nav nav-pills nav-fill mb-4" role="tablist">
            <li class="nav-item"><button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button">Masuk</button></li>
            <li class="nav-item"><button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button">Daftar</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-login">
                <?php if(isset($_GET['error'])) echo '<div class="alert alert-danger py-2 border-0"><i class="bi bi-exclamation-circle me-1"></i> Username atau password salah. Coba lagi.</div>'; ?>
                <?php if(isset($_GET['success'])) echo '<div class="alert alert-success py-2 border-0"><i class="bi bi-check-circle me-1"></i> Akun berhasil dibuat. Silakan masuk.</div>'; ?>
                <form action="index.php?page=proses-login" method="POST">
                    <div class="mb-3"><label class="form-label">Username</label><div class="field"><i class="bi bi-person"></i><input type="text" name="username" class="form-control" placeholder="Masukkan username" autocomplete="username" required></div></div>
                    <div class="mb-4"><label class="form-label">Password</label><div class="field"><i class="bi bi-lock"></i><input type="password" name="password" class="form-control" placeholder="Masukkan password" autocomplete="current-password" required><button type="button" class="eye" aria-label="Tampilkan password"><i class="bi bi-eye" style="position:static;transform:none"></i></button></div></div>
                    <button type="submit" class="btn-main">Masuk</button>
                </form>
            </div>
            <div class="tab-pane fade" id="pills-register">
                <form action="index.php?page=proses-register" method="POST">
                    <div class="mb-3"><label class="form-label">Username baru</label><div class="field"><i class="bi bi-person-plus"></i><input type="text" name="username" class="form-control" placeholder="Pilih username" autocomplete="username" required></div></div>
                    <div class="mb-4"><label class="form-label">Password baru</label><div class="field"><i class="bi bi-lock"></i><input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password" required><button type="button" class="eye" aria-label="Tampilkan password"><i class="bi bi-eye" style="position:static;transform:none"></i></button></div></div>
                    <button type="submit" class="btn-alt">Buat akun</button>
                </form>
            </div>
        </div>
    </div></section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>document.querySelectorAll('.eye').forEach(b=>b.addEventListener('click',()=>{const i=b.parentElement.querySelector('input');const show=i.type==='password';i.type=show?'text':'password';b.firstElementChild.className='bi '+(show?'bi-eye-slash':'bi-eye');b.setAttribute('aria-label',show?'Sembunyikan password':'Tampilkan password');}));</script>
</body>
</html>
