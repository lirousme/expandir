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
<body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-950 to-slate-900 text-slate-100 flex items-center justify-center px-6">
<?php if ($loggedUser): ?>
    <main class="w-full max-w-md rounded-3xl border border-white/15 bg-slate-900/70 p-8 text-center backdrop-blur-2xl shadow-[0_20px_60px_rgba(2,6,23,0.55)]">
        <h1 class="text-2xl font-semibold">Bem-vindo, <?= htmlspecialchars((string) $loggedUser, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="mt-3 text-slate-300">Você está autenticado no sistema.</p>
        <a href="/?logout=1" class="inline-flex mt-6 rounded-xl border border-white/20 bg-white/10 hover:bg-white/15 text-slate-100 font-semibold px-5 py-2 transition">Sair</a>
    </main>
<?php else: ?>
    <main class="relative w-full max-w-md">
        <div id="glassAura" class="absolute -inset-6 rounded-[2.5rem] bg-white/[0.03] blur-2xl transition-colors duration-500"></div>
        <section id="card" class="relative rounded-[2rem] border border-white/15 bg-slate-900/55 p-8 backdrop-blur-2xl shadow-[0_30px_80px_rgba(2,6,23,0.65),inset_0_1px_0_rgba(255,255,255,0.12)] transition-all duration-500 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-white/[0.03] to-transparent pointer-events-none"></div>
            <h1 id="title" class="text-2xl font-semibold text-slate-100 relative z-10">Entrar</h1>
            <p id="subtitle" class="mt-1 text-sm text-slate-300 relative z-10">Use usuário e senha para fazer login.</p>

            <form id="authForm" class="mt-8 space-y-4 relative z-10">
                <input type="hidden" id="mode" value="login" />

                <label class="block text-sm">
                    <span class="text-slate-300">Usuário</span>
                    <input id="username" name="username" type="text" autocomplete="username" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 outline-none focus:border-white/35 focus:bg-slate-950/70 transition" />
                </label>

                <label class="block text-sm">
                    <span class="text-slate-300">Senha</span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 outline-none focus:border-white/35 focus:bg-slate-950/70 transition" />
                </label>

                <button id="submitBtn" type="submit" class="w-full rounded-xl border border-white/20 bg-white/10 hover:bg-white/15 text-slate-100 font-semibold py-3 transition">Entrar</button>
            </form>

            <button id="toggleMode" type="button" class="mt-4 text-sm text-slate-300 hover:text-white transition relative z-10">Não tem conta? Criar agora</button>
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
                    accent: 'amber'
                },
                register: {
                    title: 'Criar conta',
                    subtitle: 'Escolha usuário e senha para se cadastrar.',
                    button: 'Criar conta',
                    toggle: 'Já tem conta? Fazer login',
                    accent: 'emerald'
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
            glassAura: document.getElementById('glassAura'),
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

            const isLogin = cfg.accent === 'amber';

            ui.button.className = `w-full rounded-xl border text-slate-100 font-semibold py-3 transition ${isLogin ? 'border-amber-300/40 bg-amber-300/15 hover:bg-amber-300/25' : 'border-emerald-300/35 bg-emerald-300/15 hover:bg-emerald-300/25'}`;
            ui.title.className = `text-2xl font-semibold relative z-10 ${isLogin ? 'text-amber-100' : 'text-emerald-100'}`;
            ui.toggle.className = `mt-4 text-sm transition relative z-10 ${isLogin ? 'text-amber-200/80 hover:text-amber-100' : 'text-emerald-200/80 hover:text-emerald-100'}`;

            ui.card.className = `relative rounded-[2rem] border p-8 backdrop-blur-2xl transition-all duration-500 overflow-hidden ${isLogin ? 'border-amber-200/25 bg-amber-950/35 shadow-[0_30px_80px_rgba(120,53,15,0.5),inset_0_1px_0_rgba(253,230,138,0.2)]' : 'border-emerald-200/25 bg-emerald-950/35 shadow-[0_30px_80px_rgba(6,78,59,0.55),inset_0_1px_0_rgba(110,231,183,0.18)]'}`;
            ui.glassAura.className = `absolute -inset-6 rounded-[2.5rem] blur-2xl transition-colors duration-500 ${isLogin ? 'bg-amber-300/[0.10]' : 'bg-emerald-300/[0.10]'}`;
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
