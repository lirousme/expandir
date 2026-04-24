<?php

declare(strict_types=1);

/**
 * @var string $bibliotecaUrl
 * @var array{id:int,nome_do_elemento:string} $elementoAtual
 * @var int $idElementoAtual
 * @var string $createError
 * @var string $createSuccess
 * @var array<int,array{id:int,texto_ptbr:string,nivel:int}> $slideInformacoes
 */
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Área de expansão</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<header class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-slate-900/90 backdrop-blur-xl">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-400">Área de expansão</p>
            <h1 class="text-lg font-semibold tracking-wide"><?= htmlspecialchars((string) $elementoAtual['nome_do_elemento'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>
        <div class="flex items-center gap-3">
            <button id="open-info-modal" type="button" class="rounded-lg border border-emerald-400/60 px-3 py-2 text-lg font-bold leading-none text-emerald-300 hover:bg-emerald-500/20 transition">
                +
            </button>
            <a href="<?= htmlspecialchars($bibliotecaUrl, ENT_QUOTES, 'UTF-8') ?>" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10 transition">Voltar</a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-6xl px-6 pb-8 pt-28">
    <section class="mb-6 rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-lg">
        <h2 class="text-center text-xl font-semibold">Trilha investigativa</h2>
        <p class="mt-2 text-center text-sm text-slate-400">3 informações descobertas em sequência.</p>

        <div class="mt-4 flex items-center justify-center gap-3">
            <button id="slide-prev" type="button" class="rounded-lg border border-white/20 px-3 py-2 text-sm hover:bg-white/10">←</button>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-xl border border-white/10 bg-slate-950/40">
                <?php foreach ($slideInformacoes as $index => $informacao): ?>
                    <article
                        class="investigation-slide <?= $index === 0 ? '' : 'hidden' ?> p-6"
                        data-slide-index="<?= (int) $index ?>"
                    >
                        <p class="text-xs uppercase tracking-widest text-emerald-300">Informação <?= (int) ($index + 1) ?></p>
                        <p class="mt-3 text-lg leading-relaxed text-slate-100">
                            <?= nl2br(htmlspecialchars((string) $informacao['texto_ptbr'], ENT_QUOTES, 'UTF-8')) ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
            <button id="slide-next" type="button" class="rounded-lg border border-white/20 px-3 py-2 text-sm hover:bg-white/10">→</button>
        </div>
        <div class="mt-3 flex justify-center gap-2">
            <?php foreach ($slideInformacoes as $index => $informacao): ?>
                <button
                    type="button"
                    class="slide-dot h-2.5 w-2.5 rounded-full <?= $index === 0 ? 'bg-emerald-300' : 'bg-white/30' ?>"
                    data-slide-dot="<?= (int) $index ?>"
                    aria-label="Ir para informação <?= (int) ($index + 1) ?>"
                ></button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 shadow-lg">
        <h2 class="text-xl font-semibold">Adicione uma informação</h2>
        <p class="mt-3 text-slate-300">Use o botão <strong>+</strong> no topo para criar uma nova informação e relacionar tags/elementos.</p>

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
    </section>
</main>

<div id="create-info-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/80 px-4">
    <div class="w-full max-w-xl rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Nova informação</h3>
            <button id="close-info-modal" type="button" class="rounded-md px-2 py-1 text-slate-300 hover:bg-white/10">✕</button>
        </div>

        <form method="post" class="space-y-4">
            <input type="hidden" name="action" value="criar_informacao" />
            <input type="hidden" name="tags_payload" id="tags-payload" value="[]" />

            <div>
                <label for="texto_ptbr" class="mb-2 block text-sm text-slate-300">Texto da informação</label>
                <textarea id="texto_ptbr" name="texto_ptbr" rows="4" required class="w-full rounded-lg border border-white/20 bg-slate-950 px-3 py-2 text-slate-100 outline-none ring-emerald-400/50 focus:ring"></textarea>
            </div>

            <div>
                <p class="mb-2 block text-sm text-slate-300">Tags selecionadas</p>
                <div id="selected-tags" class="flex min-h-11 flex-wrap gap-2 rounded-lg border border-white/20 bg-slate-950 p-2 text-sm text-slate-300">
                    <span class="text-slate-500">Nenhuma tag selecionada.</span>
                </div>
                <button id="open-tag-modal" type="button" class="mt-3 rounded-lg border border-white/20 px-3 py-2 text-sm hover:bg-white/10">
                    Adicionar tags
                </button>
            </div>

            <div>
                <label for="nivel" class="mb-2 block text-sm text-slate-300">Nível</label>
                <select id="nivel" name="nivel" required class="w-full rounded-lg border border-white/20 bg-slate-950 px-3 py-2 text-slate-100 outline-none ring-emerald-400/50 focus:ring">
                    <option value="1">1. Identidade</option>
                    <option value="2">2. Estrutura</option>
                    <option value="3">3. O que faz</option>
                    <option value="4">4. Como faz o que faz</option>
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="pessoal" checked class="h-4 w-4 rounded border-white/20 bg-slate-950 text-emerald-400" />
                Informação pessoal
            </label>

            <div class="flex justify-end gap-2">
                <button id="cancel-info-modal" type="button" class="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Cancelar</button>
                <button type="submit" class="rounded-lg border border-emerald-500/50 bg-emerald-500/20 px-4 py-2 text-sm font-semibold text-emerald-200 hover:bg-emerald-500/30">
                    Adicionar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="tag-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/80 px-4">
    <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Adicionar tags</h3>
            <button id="close-tag-modal" type="button" class="rounded-md px-2 py-1 text-slate-300 hover:bg-white/10">✕</button>
        </div>

        <label for="tag-search" class="mb-2 block text-sm text-slate-300">Nome do elemento</label>
        <input id="tag-search" type="text" class="w-full rounded-lg border border-white/20 bg-slate-950 px-3 py-2 text-slate-100 outline-none ring-emerald-400/50 focus:ring" placeholder="Digite para buscar ou criar..." />
        <div id="tag-suggestions" class="mt-3 max-h-52 space-y-1 overflow-y-auto rounded-lg border border-white/10 bg-slate-950 p-2 text-sm"></div>

        <div class="mt-4 flex justify-end gap-2">
            <button id="cancel-tag-modal" type="button" class="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Fechar</button>
        </div>
    </div>
</div>

<script>
    const idElementoAtual = <?= (int) $idElementoAtual ?>;
    const openInfoModalButton = document.getElementById('open-info-modal');
    const closeInfoModalButton = document.getElementById('close-info-modal');
    const cancelInfoModalButton = document.getElementById('cancel-info-modal');
    const createInfoModal = document.getElementById('create-info-modal');

    const openTagModalButton = document.getElementById('open-tag-modal');
    const closeTagModalButton = document.getElementById('close-tag-modal');
    const cancelTagModalButton = document.getElementById('cancel-tag-modal');
    const tagModal = document.getElementById('tag-modal');
    const tagSearchInput = document.getElementById('tag-search');
    const tagSuggestionsContainer = document.getElementById('tag-suggestions');
    const selectedTagsContainer = document.getElementById('selected-tags');
    const tagsPayloadInput = document.getElementById('tags-payload');

    const selectedTags = [
        { id: idElementoAtual, nome: <?= json_encode((string) $elementoAtual['nome_do_elemento'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>, novo: false }
    ];
    let mainTagKey = selectedTags.length > 0 ? `id:${selectedTags[0].id}` : null;
    let suggestionTimeout = null;
    const slides = Array.from(document.querySelectorAll('.investigation-slide'));
    const slideDots = Array.from(document.querySelectorAll('.slide-dot'));
    const prevSlideButton = document.getElementById('slide-prev');
    const nextSlideButton = document.getElementById('slide-next');
    let currentSlideIndex = 0;

    const renderCurrentSlide = () => {
        slides.forEach((slide, index) => {
            slide.classList.toggle('hidden', index !== currentSlideIndex);
        });
        slideDots.forEach((dot, index) => {
            dot.classList.toggle('bg-emerald-300', index === currentSlideIndex);
            dot.classList.toggle('bg-white/30', index !== currentSlideIndex);
        });
    };

    const goToSlide = (index) => {
        if (slides.length === 0) {
            return;
        }
        currentSlideIndex = (index + slides.length) % slides.length;
        renderCurrentSlide();
    };

    if (prevSlideButton) {
        prevSlideButton.addEventListener('click', () => goToSlide(currentSlideIndex - 1));
    }
    if (nextSlideButton) {
        nextSlideButton.addEventListener('click', () => goToSlide(currentSlideIndex + 1));
    }
    slideDots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
    });

    const openModal = (element) => {
        element.classList.remove('hidden');
        element.classList.add('flex');
    };

    const closeModal = (element) => {
        element.classList.add('hidden');
        element.classList.remove('flex');
    };

    const buildTagKey = (tag) => (Number(tag.id || 0) > 0 ? `id:${Number(tag.id)}` : `name:${String(tag.nome || '').trim().toLowerCase()}`);

    const ensureMainTagSelected = () => {
        if (selectedTags.length === 0) {
            mainTagKey = null;
            return;
        }

        const hasCurrentMain = selectedTags.some((tag) => buildTagKey(tag) === mainTagKey);
        if (!hasCurrentMain) {
            mainTagKey = buildTagKey(selectedTags[0]);
        }
    };

    const syncTagsPayload = () => {
        ensureMainTagSelected();
        tagsPayloadInput.value = JSON.stringify(
            selectedTags.map((tag) => ({
                ...tag,
                main: buildTagKey(tag) === mainTagKey ? 1 : 2,
            }))
        );
    };

    const renderSelectedTags = () => {
        ensureMainTagSelected();
        selectedTagsContainer.innerHTML = '';

        if (selectedTags.length === 0) {
            selectedTagsContainer.innerHTML = '<span class="text-slate-500">Nenhuma tag selecionada.</span>';
            syncTagsPayload();
            return;
        }

        selectedTags.forEach((tag, index) => {
            const tagIsMain = buildTagKey(tag) === mainTagKey;
            const wrapper = document.createElement('div');
            wrapper.className = tagIsMain
                ? 'inline-flex items-center gap-1 rounded-full border border-[orangered]/60 bg-[orangered]/20 px-2 py-1 text-xs text-[orangered]'
                : 'inline-flex items-center gap-1 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-2 py-1 text-xs text-emerald-200';

            const tagButton = document.createElement('button');
            tagButton.type = 'button';
            tagButton.className = 'rounded-full px-1 py-0.5 hover:bg-white/10';
            tagButton.textContent = tag.nome;
            tagButton.title = 'Clique para definir como tag principal';
            tagButton.addEventListener('click', () => {
                mainTagKey = buildTagKey(tag);
                renderSelectedTags();
            });

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'rounded-full px-1 py-0.5 text-slate-200 hover:bg-white/10';
            removeButton.textContent = '✕';
            removeButton.title = 'Remover tag';
            removeButton.addEventListener('click', () => {
                selectedTags.splice(index, 1);
                renderSelectedTags();
            });

            wrapper.appendChild(tagButton);
            wrapper.appendChild(removeButton);
            selectedTagsContainer.appendChild(wrapper);
        });

        syncTagsPayload();
    };

    const isTagAlreadySelected = (tagId, tagName) =>
        selectedTags.some((tag) => (tagId > 0 && tag.id === tagId) || tag.nome.toLowerCase() === tagName.toLowerCase());

    const addTag = (tag) => {
        if (isTagAlreadySelected(Number(tag.id || 0), tag.nome || '')) {
            return;
        }
        selectedTags.push(tag);
        if (selectedTags.length === 1) {
            mainTagKey = buildTagKey(tag);
        }
        renderSelectedTags();
    };

    const renderSuggestions = (items, query) => {
        tagSuggestionsContainer.innerHTML = '';

        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'block w-full rounded-md px-3 py-2 text-left text-slate-200 hover:bg-white/10';
            button.textContent = item.nome_do_elemento;
            button.addEventListener('click', () => {
                addTag({ id: Number(item.id), nome: item.nome_do_elemento, novo: false });
                tagSearchInput.value = '';
                tagSuggestionsContainer.innerHTML = '';
            });
            tagSuggestionsContainer.appendChild(button);
        });

        const normalizedQuery = query.trim();
        if (normalizedQuery !== '') {
            const createButton = document.createElement('button');
            createButton.type = 'button';
            createButton.className = 'mt-2 block w-full rounded-md border border-dashed border-emerald-400/40 px-3 py-2 text-left text-emerald-200 hover:bg-emerald-500/10';
            createButton.textContent = `Criar nova tag: "${normalizedQuery}"`;
            createButton.addEventListener('click', () => {
                addTag({ id: 0, nome: normalizedQuery, novo: true });
                tagSearchInput.value = '';
                tagSuggestionsContainer.innerHTML = '';
            });
            tagSuggestionsContainer.appendChild(createButton);
        }
    };

    const buscarSugestoes = async (query) => {
        if (query.trim() === '') {
            renderSuggestions([], '');
            return;
        }

        const response = await fetch(`area_de_expansao.php?id_elemento=${idElementoAtual}&action=sugestoes_elementos&q=${encodeURIComponent(query)}`);
        if (!response.ok) {
            renderSuggestions([], query);
            return;
        }

        const data = await response.json();
        const items = Array.isArray(data.items) ? data.items : [];
        renderSuggestions(items, query);
    };

    openInfoModalButton?.addEventListener('click', () => openModal(createInfoModal));
    closeInfoModalButton?.addEventListener('click', () => closeModal(createInfoModal));
    cancelInfoModalButton?.addEventListener('click', () => closeModal(createInfoModal));

    openTagModalButton?.addEventListener('click', () => {
        openModal(tagModal);
        tagSearchInput.focus();
    });
    closeTagModalButton?.addEventListener('click', () => closeModal(tagModal));
    cancelTagModalButton?.addEventListener('click', () => closeModal(tagModal));

    tagSearchInput?.addEventListener('input', (event) => {
        const query = event.target.value || '';
        if (suggestionTimeout) {
            clearTimeout(suggestionTimeout);
        }
        suggestionTimeout = setTimeout(() => buscarSugestoes(query), 200);
    });

    [createInfoModal, tagModal].forEach((modal) => {
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    renderSelectedTags();
</script>
</body>
</html>
