<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

$loggedUser = $_SESSION['auth_user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Auth Mirror</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center px-6">
<?php if ($loggedUser): ?>
    <main class="w-full max-w-md rounded-3xl border border-cyan-400/40 bg-slate-900/70 p-8 text-center backdrop-blur-xl shadow-[0_0_35px_rgba(34,211,238,0.35)]">
        <h1 class="text-2xl font-semibold">Bem-vindo, <?= htmlspecialchars((string) $loggedUser, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="mt-3 text-slate-300">Você está autenticado no sistema.</p>
        <a href="/?logout=1" class="inline-flex mt-6 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-semibold px-5 py-2 transition">Sair</a>
    </main>
<?php else: ?>
    <main class="relative w-full max-w-md">
        <div id="glow" class="absolute -inset-8 rounded-[2.5rem] blur-3xl bg-blue-500/20 transition-colors duration-500"></div>
        <section id="card" class="relative rounded-[2rem] border border-blue-400/40 bg-slate-900/60 p-8 backdrop-blur-2xl shadow-[inset_0_0_30px_rgba(255,255,255,0.09),0_0_40px_rgba(37,99,235,0.35)] transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-white/5 pointer-events-none"></div>
            <h1 id="title" class="text-2xl font-semibold text-blue-300 relative z-10">Entrar</h1>
            <p id="subtitle" class="mt-1 text-sm text-slate-300 relative z-10">Use usuário e senha para fazer login.</p>

            <form id="authForm" class="mt-8 space-y-4 relative z-10">
                <input type="hidden" id="mode" value="login" />

                <label class="block text-sm">
                    <span class="text-slate-300">Usuário</span>
                    <input id="username" name="username" type="text" autocomplete="username" required class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950/70 px-4 py-3 outline-none focus:border-blue-400 transition" />
                </label>

                <label class="block text-sm">
                    <span class="text-slate-300">Senha</span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-950/70 px-4 py-3 outline-none focus:border-blue-400 transition" />
                </label>

                <button id="submitBtn" type="submit" class="w-full rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-semibold py-3 transition">Entrar</button>
            </form>

            <button id="toggleMode" type="button" class="mt-4 text-sm text-blue-300 hover:text-blue-200 transition relative z-10">Não tem conta? Criar agora</button>
            <p id="feedback" class="mt-4 min-h-6 text-sm text-slate-300 relative z-10"></p>
        </section>
    </main>

    <script>
        const state = {
            mode: 'login',
            palette: {
                login: {
                    title: 'Entrar',
                    subtitle: 'Use usuário e senha para fazer login.',
                    button: 'Entrar',
                    toggle: 'Não tem conta? Criar agora',
                    accent: 'blue'
                },
                register: {
                    title: 'Criar conta',
                    subtitle: 'Escolha usuário e senha para se cadastrar.',
                    button: 'Criar conta',
                    toggle: 'Já tem conta? Fazer login',
                    accent: 'green'
                }
            }
        };

        const ui = {
            mode: document.getElementById('mode'),
            title: document.getElementById('title'),
            subtitle: document.getElementById('subtitle'),
            button: document.getElementById('submitBtn'),
            toggle: document.getElementById('toggleMode'),
            card: document.getElementById('card'),
            glow: document.getElementById('glow'),
            feedback: document.getElementById('feedback'),
            form: document.getElementById('authForm'),
            username: document.getElementById('username'),
            password: document.getElementById('password')
        };

        function applyMode(mode) {
            state.mode = mode;
            const cfg = state.palette[mode];

            ui.mode.value = mode;
            ui.title.textContent = cfg.title;
            ui.subtitle.textContent = cfg.subtitle;
            ui.button.textContent = cfg.button;
            ui.toggle.textContent = cfg.toggle;

            ui.button.className = `w-full rounded-xl text-slate-950 font-semibold py-3 transition ${cfg.accent === 'blue' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-emerald-500 hover:bg-emerald-400'}`;
            ui.title.className = `text-2xl font-semibold relative z-10 ${cfg.accent === 'blue' ? 'text-blue-300' : 'text-emerald-300'}`;
            ui.toggle.className = `mt-4 text-sm transition relative z-10 ${cfg.accent === 'blue' ? 'text-blue-300 hover:text-blue-200' : 'text-emerald-300 hover:text-emerald-200'}`;

            ui.card.className = `relative rounded-[2rem] border p-8 backdrop-blur-2xl transition-all duration-500 overflow-hidden ${cfg.accent === 'blue' ? 'border-blue-400/40 bg-slate-900/60 shadow-[inset_0_0_30px_rgba(255,255,255,0.09),0_0_40px_rgba(37,99,235,0.35)]' : 'border-emerald-400/40 bg-slate-900/60 shadow-[inset_0_0_30px_rgba(255,255,255,0.09),0_0_40px_rgba(16,185,129,0.35)]'}`;
            ui.glow.className = `absolute -inset-8 rounded-[2.5rem] blur-3xl transition-colors duration-500 ${cfg.accent === 'blue' ? 'bg-blue-500/20' : 'bg-emerald-500/20'}`;
            ui.feedback.textContent = '';
        }

        document.getElementById('toggleMode').addEventListener('click', () => {
            applyMode(state.mode === 'login' ? 'register' : 'login');
        });

        ui.form.addEventListener('submit', async (event) => {
            event.preventDefault();
            ui.feedback.textContent = 'Processando...';

            try {
                const response = await fetch('/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        mode: ui.mode.value,
                        username: ui.username.value,
                        password: ui.password.value
                    })
                });

                const data = await response.json();
                ui.feedback.textContent = data.message || 'Resposta inesperada.';

                if (response.ok) {
                    window.location.reload();
                }
            } catch {
                ui.feedback.textContent = 'Falha de comunicação com o servidor.';
            }
        });
    </script>
<?php endif; ?>
</body>
</html>
