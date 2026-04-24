<?php

declare(strict_types=1);

/**
 * @var string $username
 * @var string $logoutUrl
 */
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<header class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-slate-900/90 backdrop-blur-xl">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <h1 class="text-lg font-semibold tracking-wide">Biblioteca</h1>
        <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10 transition">Sair</a>
    </div>
</header>

<main class="mx-auto max-w-6xl px-6 pb-8 pt-28">
    <section class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-lg">
        <h2 class="text-xl font-semibold">Bem-vindo, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!</h2>
        <p class="mt-3 text-slate-300">Você entrou na página Biblioteca após o login. O cabeçalho acima fica fixo no topo da tela.</p>
    </section>
</main>
</body>
</html>
