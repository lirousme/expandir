<?php

declare(strict_types=1);

/**
 * @var string $username
 * @var string $logoutUrl
 * @var string $areaDeExpansaoBaseUrl
 * @var string $createError
 * @var string $createSuccess
 * @var array<int,array{id:int,nome_do_elemento:string,ordem:int}> $elementosDaBiblioteca
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
        <div class="flex items-center gap-3">
            <button id="open-create-element-modal" type="button" class="rounded-lg border border-emerald-400/60 px-3 py-2 text-lg font-bold leading-none text-emerald-300 hover:bg-emerald-500/20 transition" aria-haspopup="dialog" aria-controls="create-element-modal">
                +
            </button>
            <a href="<?= htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8') ?>" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10 transition">Sair</a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-6xl px-6 pb-8 pt-28">
    <section class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-lg">
        <h2 class="text-xl font-semibold">Bem-vindo, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!</h2>
        <p class="mt-3 text-slate-300">Os elementos exibidos abaixo são filtrados pela tabela biblioteca.</p>

        <?php if ($createSuccess !== ''): ?>
            <p class="mt-4 rounded-lg border border-emerald-400/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                <?= htmlspecialchars($createSuccess, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($createError !== ''): ?>
            <p class="mt-4 rounded-lg border border-rose-400/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <?= htmlspecialchars($createError, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <div class="mt-6 overflow-hidden rounded-xl border border-white/10">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm">
                <thead class="bg-slate-800/80 text-slate-300">
                <tr>
                    <th class="px-4 py-3 font-medium">Ordem</th>
                    <th class="px-4 py-3 font-medium">Elemento</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                <?php if ($elementosDaBiblioteca === []): ?>
                    <tr>
                        <td class="px-4 py-4 text-slate-400" colspan="2">Nenhum elemento na biblioteca ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($elementosDaBiblioteca as $elemento): ?>
                        <tr class="bg-slate-900/30">
                            <td class="px-4 py-3 text-slate-300"><?= (int) $elemento['ordem'] ?></td>
                            <td class="px-4 py-3">
                                <a
                                    href="<?= htmlspecialchars($areaDeExpansaoBaseUrl . '?id_elemento=' . (int) $elemento['id'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="text-emerald-300 hover:text-emerald-200 hover:underline"
                                >
                                    <?= htmlspecialchars((string) $elemento['nome_do_elemento'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="create-element-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/80 px-4">
    <div class="w-full max-w-md rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Novo elemento</h3>
            <button id="close-create-element-modal" type="button" class="rounded-md px-2 py-1 text-slate-300 hover:bg-white/10">✕</button>
        </div>

        <form method="post" class="space-y-4">
            <div>
                <label for="nome_do_elemento" class="mb-2 block text-sm text-slate-300">Nome do elemento</label>
                <input id="nome_do_elemento" name="nome_do_elemento" type="text" required class="w-full rounded-lg border border-white/20 bg-slate-950 px-3 py-2 text-slate-100 outline-none ring-emerald-400/50 focus:ring" />
            </div>
            <div class="flex justify-end gap-2">
                <button id="cancel-create-element-modal" type="button" class="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Cancelar</button>
                <button type="submit" class="rounded-lg border border-emerald-500/50 bg-emerald-500/20 px-4 py-2 text-sm font-semibold text-emerald-200 hover:bg-emerald-500/30">
                    Criar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const openCreateElementModalButton = document.getElementById('open-create-element-modal');
    const closeCreateElementModalButton = document.getElementById('close-create-element-modal');
    const cancelCreateElementModalButton = document.getElementById('cancel-create-element-modal');
    const createElementModal = document.getElementById('create-element-modal');

    const openCreateElementModal = () => {
        createElementModal.classList.remove('hidden');
        createElementModal.classList.add('flex');
    };

    const closeCreateElementModal = () => {
        createElementModal.classList.add('hidden');
        createElementModal.classList.remove('flex');
    };

    openCreateElementModalButton?.addEventListener('click', openCreateElementModal);
    closeCreateElementModalButton?.addEventListener('click', closeCreateElementModal);
    cancelCreateElementModalButton?.addEventListener('click', closeCreateElementModal);
    createElementModal?.addEventListener('click', (event) => {
        if (event.target === createElementModal) {
            closeCreateElementModal();
        }
    });
</script>
</body>
</html>
