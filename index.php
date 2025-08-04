<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Family Trees</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    
    <!-- Language Toggle -->
    <div class="fixed top-4 right-4 z-50 flex space-x-2">
        <button class="flag-btn active bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg hover:bg-white/30 transition-all duration-300" onclick="switchLanguage('en')" id="en-flag" title="English">🇺🇸</button>
        <button class="flag-btn bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg hover:bg-white/30 transition-all duration-300" onclick="switchLanguage('pt')" id="pt-flag" title="Português">🇧🇷</button>
        <button class="flag-btn bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg hover:bg-white/30 transition-all duration-300" onclick="switchLanguage('et')" id="et-flag" title="Eesti">🇪🇪</button>
        <button class="flag-btn bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg hover:bg-white/30 transition-all duration-300" onclick="switchLanguage('fr')" id="fr-flag" title="Français">🇫🇷</button>
        <button class="flag-btn bg-white/20 backdrop-blur-sm px-3 py-2 rounded-lg hover:bg-white/30 transition-all duration-300" onclick="switchLanguage('de')" id="de-flag" title="Deutsch">🇩🇪</button>
    </div>

    <div class="container mx-auto px-4 py-12">
        <header class="main-header">
            <h1 class="main-title" data-translate="main-title">Árvores Genealógicas</h1>
            <p class="main-subtitle" data-translate="main-subtitle">Gerencie suas árvores genealógicas familiares</p>
        </header>

        <div id="trees-container" class="trees-container">
            <!-- Trees will be loaded here -->
        </div>
    </div>

    <!-- Family Photo Menu -->
    <div class="photo-menu" id="familyPhotoMenu" style="display: none;">
        <div class="photo-menu-option" onclick="viewFamilyPhoto()">
            <span>👁️</span>
            <span data-translate="view-photo">Visualizar</span>
        </div>
        <div class="photo-menu-option" onclick="enterFamilyTree()">
            <span>🌳</span>
            <span data-translate="enter-family-tree">Entrar na Árvore</span>
        </div>
    </div>

    <!-- Family Photo Preview Modal -->
    <div class="modal family-photo-modal" id="familyPhotoModal">
        <div class="family-photo-modal-content">
            <div class="family-photo-header">
                <h3 id="familyPhotoTitle"></h3>
                <button onclick="closeFamilyPhotoModal()" class="family-photo-close">×</button>
            </div>
            <div class="family-photo-image-container">
                <img id="familyPhotoPreview" src="" alt="Family Tree Cover">
            </div>
            <div class="family-photo-actions">
                <button onclick="enterFamilyTreeFromModal()" class="family-photo-enter-btn" data-translate="enter-family-tree">Entrar na Árvore</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Tree Modal -->
    <div id="treeModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" class="text-2xl font-bold mb-6" data-translate="add-tree-title">Adicionar Nova Árvore</h2>
            <form id="treeForm">
                <input type="hidden" id="treeId" name="treeId">
                
                <div class="mb-6">
                    <label class="block text-gray-800 font-semibold mb-3 text-sm uppercase tracking-wide" data-translate="tree-title-label">Título da Árvore:</label>
                    <input type="text" id="treeTitle" name="treeTitle" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white" placeholder="Ex: Família Silva" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-800 font-semibold mb-3 text-sm uppercase tracking-wide" data-translate="tree-subtitle-label">Descrição:</label>
                    <textarea id="treeSubtitle" name="treeSubtitle" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-gray-50 focus:bg-white resize-none" placeholder="pt: Descrição em português&#10;en: Description in English&#10;de: Beschreibung auf Deutsch&#10;fr: Description en français&#10;et: Kirjeldus eesti keeles"></textarea>
                    <p class="text-sm text-gray-500 mt-2" data-translate="subtitle-format-hint">Use o formato: pt: texto, en: text, de: text, fr: text</p>
                </div>
                
                <div class="mb-8">
                    <label class="block text-gray-800 font-semibold mb-3 text-sm uppercase tracking-wide" data-translate="tree-image-label">Imagem da Árvore:</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-purple-300 transition-colors">
                        <input type="file" id="treeImage" name="treeImage" accept="image/*" class="hidden">
                        <div onclick="document.getElementById('treeImage').click()" class="cursor-pointer">
                            <div class="w-16 h-16 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-medium" data-translate="select-image-btn">Selecionar Imagem</p>
                            <p class="text-gray-400 text-sm mt-1" data-translate="image-format-info">PNG, JPG ou WebP até 5MB</p>
                        </div>
                        <span id="imageFileName" class="text-purple-600 text-sm font-medium mt-2 block"></span>
                    </div>
                    <div id="imagePreview" class="mt-4 hidden">
                        <img id="previewImg" src="" alt="Preview" class="w-40 h-40 object-cover rounded-xl mx-auto shadow-lg">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium" data-translate="cancel-btn">Cancelar</button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all font-medium shadow-lg transform hover:scale-105" data-translate="save-btn">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Translation data
        const translations = {
            en: {
                "main-title": "Family Trees",
                "main-subtitle": "Manage your family genealogical trees",
                "add-tree-title": "Add New Tree",
                "edit-tree-title": "Edit Tree",
                "tree-title-label": "Tree Title:",
                "tree-subtitle-label": "Description:",
                "tree-image-label": "Tree Image:",
                "select-image-btn": "Select Image",
                "image-format-info": "PNG, JPG or WebP up to 5MB",
                "cancel-btn": "Cancel",
                "save-btn": "Save",
                "add-new-tree": "Add New Tree",
                "click-to-add": "Click to create your first family tree",
                "create-new-label": "Create new family tree",
                "updated-label": "Updated",
                "view-photo": "View Photo",
                "enter-family-tree": "Enter Family Tree",
                "delete-confirm": "Are you sure you want to delete",
                "delete-warning": "This action cannot be undone and will delete all family data in this tree.",
                "delete-error": "Error deleting tree",
                "delete-error-retry": "Error deleting tree. Please try again.",
                "no-image-message": "This tree does not have a cover image.",
                "save-error": "Error",
                "save-error-retry": "Error saving tree. Please try again.",
                "subtitle-format-hint": "Use the format: pt: text, en: text, de: text, fr: text"
            },
            pt: {
                "main-title": "Árvores Genealógicas",
                "main-subtitle": "Gerencie suas árvores genealógicas familiares",
                "add-tree-title": "Adicionar Nova Árvore",
                "edit-tree-title": "Editar Árvore",
                "tree-title-label": "Título da Árvore:",
                "tree-subtitle-label": "Descrição:",
                "tree-image-label": "Imagem da Árvore:",
                "select-image-btn": "Selecionar Imagem",
                "image-format-info": "PNG, JPG ou WebP até 5MB",
                "cancel-btn": "Cancelar",
                "save-btn": "Salvar",
                "add-new-tree": "Adicionar Nova Árvore",
                "click-to-add": "Clique para criar sua primeira árvore genealógica",
                "create-new-label": "Criar nova árvore genealógica",
                "updated-label": "Atualizado",
                "view-photo": "Visualizar Foto",
                "enter-family-tree": "Entrar na Árvore",
                "delete-confirm": "Tem certeza que deseja excluir",
                "delete-warning": "Esta ação não pode ser desfeita e excluirá todos os dados familiares desta árvore.",
                "delete-error": "Erro ao excluir árvore",
                "delete-error-retry": "Erro ao excluir árvore. Tente novamente.",
                "no-image-message": "Esta árvore não possui uma imagem de capa.",
                "save-error": "Erro",
                "save-error-retry": "Erro ao salvar árvore. Tente novamente.",
                "subtitle-format-hint": "Use o formato: pt: texto, en: text, de: text, fr: text"
            },
            de: {
                "main-title": "Stammbäume",
                "main-subtitle": "Verwalten Sie Ihre Familienstammbäume",
                "add-tree-title": "Neuen Baum Hinzufügen",
                "edit-tree-title": "Baum Bearbeiten",
                "tree-title-label": "Baumtitel:",
                "tree-subtitle-label": "Beschreibung:",
                "tree-image-label": "Baumbild:",
                "select-image-btn": "Bild Auswählen",
                "image-format-info": "PNG, JPG oder WebP bis 5MB",
                "cancel-btn": "Abbrechen",
                "save-btn": "Speichern",
                "add-new-tree": "Neuen Baum Hinzufügen",
                "click-to-add": "Klicken Sie, um Ihren ersten Stammbaum zu erstellen",
                "create-new-label": "Neuen Stammbaum erstellen",
                "updated-label": "Aktualisiert",
                "view-photo": "Foto Ansehen",
                "enter-family-tree": "Stammbaum Betreten",
                "delete-confirm": "Sind Sie sicher, dass Sie löschen möchten",
                "delete-warning": "Diese Aktion kann nicht rückgängig gemacht werden und löscht alle Familiendaten in diesem Baum.",
                "delete-error": "Fehler beim Löschen des Baums",
                "delete-error-retry": "Fehler beim Löschen des Baums. Bitte versuchen Sie es erneut.",
                "no-image-message": "Dieser Baum hat kein Coverbild.",
                "save-error": "Fehler",
                "save-error-retry": "Fehler beim Speichern des Baums. Bitte versuchen Sie es erneut.",
                "subtitle-format-hint": "Verwenden Sie das Format: pt: text, en: text, de: text, fr: text"
            },
            fr: {
                "main-title": "Arbres Généalogiques",
                "main-subtitle": "Gérez vos arbres généalogiques familiaux",
                "add-tree-title": "Ajouter un Nouvel Arbre",
                "edit-tree-title": "Modifier l'Arbre",
                "tree-title-label": "Titre de l'Arbre:",
                "tree-subtitle-label": "Description:",
                "tree-image-label": "Image de l'Arbre:",
                "select-image-btn": "Sélectionner une Image",
                "image-format-info": "PNG, JPG ou WebP jusqu'à 5MB",
                "cancel-btn": "Annuler",
                "save-btn": "Enregistrer",
                "add-new-tree": "Ajouter un Nouvel Arbre",
                "click-to-add": "Cliquez pour créer votre premier arbre généalogique",
                "create-new-label": "Créer un nouvel arbre généalogique",
                "updated-label": "Mis à jour",
                "view-photo": "Voir la Photo",
                "enter-family-tree": "Entrer dans l'Arbre",
                "delete-confirm": "Êtes-vous sûr de vouloir supprimer",
                "delete-warning": "Cette action ne peut pas être annulée et supprimera toutes les données familiales de cet arbre.",
                "delete-error": "Erreur lors de la suppression de l'arbre",
                "delete-error-retry": "Erreur lors de la suppression de l'arbre. Veuillez réessayer.",
                "no-image-message": "Cet arbre n'a pas d'image de couverture.",
                "save-error": "Erreur",
                "save-error-retry": "Erreur lors de l'enregistrement de l'arbre. Veuillez réessayer.",
                "subtitle-format-hint": "Utilisez le format: pt: texte, en: text, de: text, fr: text"
            },
            et: {
                "main-title": "Sugupuud",
                "main-subtitle": "Halda oma perekonna sugupuid",
                "add-tree-title": "Lisa uus puu",
                "edit-tree-title": "Muuda puud",
                "tree-title-label": "Puu pealkiri:",
                "tree-subtitle-label": "Kirjeldus:",
                "tree-image-label": "Puu pilt:",
                "select-image-btn": "Vali pilt",
                "image-format-info": "PNG, JPG või WebP kuni 5MB",
                "cancel-btn": "Tühista",
                "save-btn": "Salvesta",
                "add-new-tree": "Lisa uus puu",
                "click-to-add": "Klõpsa, et luua oma esimene sugupuu",
                "create-new-label": "Loo uus sugupuu",
                "updated-label": "Uuendatud",
                "view-photo": "Vaata pilti",
                "enter-family-tree": "Sisene sugupuusse",
                "delete-confirm": "Kas oled kindel, et soovid kustutada",
                "delete-warning": "Seda tegevust ei saa tagasi võtta ja see kustutab kõik selle puu perekonna andmed.",
                "delete-error": "Viga puu kustutamisel",
                "delete-error-retry": "Viga puu kustutamisel. Palun proovi uuesti.",
                "no-image-message": "Sellel puul pole kaanepilti.",
                "save-error": "Viga",
                "save-error-retry": "Viga puu salvestamisel. Palun proovi uuesti.",
                "subtitle-format-hint": "Kasuta vormi: pt: tekst, en: text, de: text, fr: text, et: tekst"
            }
        };

        let currentLanguage = 'pt';
        let trees = [];
        let currentSelectedTreeId = null;
        let currentSelectedTreeImage = null;
        let currentSelectedTreeTitle = null;

        // Function to parse multilingual text format (pt: text, en: text, etc.)
        function parseMultilingualText(text) {
            const result = {};
            if (!text) return result;
            
            const lines = text.split('\n');
            lines.forEach(line => {
                const match = line.match(/^(pt|en|de|fr|et):\s*(.+)$/);
                if (match) {
                    const [, lang, content] = match;
                    result[lang] = content.trim();
                }
            });
            
            return result;
        }

        function switchLanguage(lang) {
            currentLanguage = lang;
            document.documentElement.lang = lang;
            
            // Update URL parameter
            const url = new URL(window.location);
            url.searchParams.set('lang', lang);
            window.history.replaceState({}, '', url);
            
            // Update active flag
            document.querySelectorAll('.flag-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(lang + '-flag').classList.add('active');
            
            // Update all translatable elements
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                if (translations[lang] && translations[lang][key]) {
                    if (element.tagName === 'INPUT' && element.type === 'button') {
                        element.value = translations[lang][key];
                    } else {
                        element.textContent = translations[lang][key];
                    }
                }
            });

            // Re-render trees with new language
            renderTrees();
        }

        async function loadTrees() {
            try {
                const response = await fetch('trees.json?v=' + Date.now());
                const data = await response.json();
                trees = data.trees || [];
                renderTrees();
            } catch (error) {
                console.error('Error loading trees:', error);
                trees = [];
                renderTrees();
            }
        }

        function renderTrees() {
            const container = document.getElementById('trees-container');
            container.innerHTML = '';

            // Add "Add New Tree" card first
            const addCard = createAddTreeCard();
            container.appendChild(addCard);

            // Render existing trees
            trees.forEach(tree => {
                const treeCard = createTreeCard(tree);
                container.appendChild(treeCard);
            });
        }

        function createTreeCard(tree) {
            const card = document.createElement('div');
            card.className = 'tree-card family-tree-card';
            card.onclick = () => openTree(tree.id);

            const title = tree.title[currentLanguage] || tree.title.en || Object.values(tree.title)[0];
            const subtitle = tree.subtitle[currentLanguage] || tree.subtitle.en || Object.values(tree.subtitle)[0];

            card.innerHTML = `
                <div class="tree-image" onclick="showFamilyPhotoMenu(event, '${tree.id}', '${tree.image || ''}', '${encodeURIComponent(title)}')">
                    ${tree.image ? `<img src="${tree.image}" alt="Tree Image">` : '🌳'}
                </div>
                <div class="tree-content">
                    <h3 class="tree-title">${title}</h3>
                    <p class="tree-subtitle">${subtitle}</p>
                    <div class="tree-footer">
                        <span class="tree-date">${translations[currentLanguage]['updated-label']}: ${new Date(tree.updatedAt).toLocaleDateString()}</span>
                        <div class="tree-actions">
                            <button class="action-btn" onclick="editTree('${tree.id}'); event.stopPropagation();" title="Edit Tree">
                                ✏️
                            </button>
                            <button class="action-btn" onclick="deleteTree('${tree.id}'); event.stopPropagation();" title="Delete Tree" style="color: #e53e3e;">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            `;

            return card;
        }

        function createAddTreeCard() {
            const card = document.createElement('div');
            card.className = 'add-tree-card tree-card family-tree-card';
            card.onclick = () => showAddModal();

            card.innerHTML = `
                <div class="tree-image">
                    <div class="add-tree-icon">+</div>
                </div>
                <div class="tree-content">
                    <h3 class="tree-title">${translations[currentLanguage]['add-new-tree']}</h3>
                    <p class="tree-subtitle">${translations[currentLanguage]['click-to-add']}</p>
                    <div class="tree-footer">
                        <span class="tree-date">${translations[currentLanguage]['create-new-label']}</span>
                        <div class="tree-actions">
                            <div class="action-btn" style="background: rgba(255,255,255,0.2); color: white;">
                                ➕
                            </div>
                        </div>
                    </div>
                </div>
            `;

            return card;
        }

        function showAddModal() {
            document.getElementById('modalTitle').setAttribute('data-translate', 'add-tree-title');
            document.getElementById('modalTitle').textContent = translations[currentLanguage]['add-tree-title'];
            document.getElementById('treeForm').reset();
            document.getElementById('treeId').value = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('treeModal').style.display = 'block';
        }

        function editTree(treeId) {
            const tree = trees.find(t => t.id === treeId);
            if (!tree) return;

            document.getElementById('modalTitle').setAttribute('data-translate', 'edit-tree-title');
            document.getElementById('modalTitle').textContent = translations[currentLanguage]['edit-tree-title'];
            
            document.getElementById('treeId').value = tree.id;
            document.getElementById('treeTitle').value = tree.title[currentLanguage] || tree.title.en || Object.values(tree.title)[0];
            
            // Format subtitle as multilingual text
            let subtitleText = '';
            if (tree.subtitle) {
                const languages = ['pt', 'en', 'de', 'fr', 'et'];
                const subtitleParts = [];
                languages.forEach(lang => {
                    if (tree.subtitle[lang]) {
                        subtitleParts.push(`${lang}: ${tree.subtitle[lang]}`);
                    }
                });
                subtitleText = subtitleParts.join('\n');
            }
            document.getElementById('treeSubtitle').value = subtitleText;
            
            if (tree.image) {
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('previewImg').src = tree.image;
            }
            
            document.getElementById('treeModal').style.display = 'block';
        }

        function openTree(treeId) {
            window.location.href = `tree.php?id=${treeId}&lang=${currentLanguage}`;
        }

        async function deleteTree(treeId) {
            const tree = trees.find(t => t.id === treeId);
            if (!tree) return;

            const title = tree.title[currentLanguage] || tree.title.en || Object.values(tree.title)[0];
            const confirmMessage = translations[currentLanguage]['delete-confirm'] + ` "${title}"? ` + translations[currentLanguage]['delete-warning'];
            const confirmDelete = confirm(confirmMessage);
            
            if (confirmDelete) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('treeId', treeId);
                    
                    const response = await fetch('manage_trees.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        loadTrees();
                    } else {
                        alert(translations[currentLanguage]['delete-error'] + ': ' + result.error);
                    }
                } catch (error) {
                    console.error('Error deleting tree:', error);
                    alert(translations[currentLanguage]['delete-error-retry']);
                }
            }
        }

        function closeModal() {
            document.getElementById('treeModal').style.display = 'none';
        }

        function showFamilyPhotoMenu(event, treeId, image, title) {
            event.stopPropagation(); // Prevent card click from firing
            
            currentSelectedTreeId = treeId;
            currentSelectedTreeImage = image;
            currentSelectedTreeTitle = decodeURIComponent(title);
            
            const menu = document.getElementById('familyPhotoMenu');
            const rect = event.currentTarget.getBoundingClientRect();
            
            // Position menu at the center of the clicked image
            menu.style.left = (rect.left + rect.width / 2 - 75) + 'px';
            menu.style.top = (rect.bottom + 10) + 'px';
            menu.style.display = 'block';
            
            // Hide menu when clicking elsewhere
            setTimeout(() => {
                document.addEventListener('click', hideFamilyPhotoMenu);
            }, 100);
        }

        function hideFamilyPhotoMenu() {
            document.getElementById('familyPhotoMenu').style.display = 'none';
            document.removeEventListener('click', hideFamilyPhotoMenu);
        }

        function viewFamilyPhoto() {
            hideFamilyPhotoMenu();
            
            if (currentSelectedTreeImage && currentSelectedTreeImage !== 'null' && currentSelectedTreeImage !== '') {
                // Show photo modal
                document.getElementById('familyPhotoTitle').textContent = currentSelectedTreeTitle;
                document.getElementById('familyPhotoPreview').src = currentSelectedTreeImage;
                document.getElementById('familyPhotoModal').style.display = 'block';
            } else {
                // Show a placeholder or message for no image
                alert(translations[currentLanguage]['no-image-message']);
            }
        }

        function enterFamilyTree() {
            hideFamilyPhotoMenu();
            openTree(currentSelectedTreeId);
        }

        function enterFamilyTreeFromModal() {
            closeFamilyPhotoModal();
            openTree(currentSelectedTreeId);
        }

        function closeFamilyPhotoModal() {
            document.getElementById('familyPhotoModal').style.display = 'none';
        }

        // Image preview functionality
        document.getElementById('treeImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('imageFileName').textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('treeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const treeId = document.getElementById('treeId').value;
            const title = document.getElementById('treeTitle').value;
            const subtitleRaw = document.getElementById('treeSubtitle').value;
            const imageFile = document.getElementById('treeImage').files[0];
            
            // Parse multilingual subtitle format
            const parsedSubtitle = parseMultilingualText(subtitleRaw);
            
            formData.append('action', treeId ? 'edit' : 'add');
            formData.append('treeId', treeId);
            formData.append('title', title);
            formData.append('subtitle', JSON.stringify(parsedSubtitle));
            formData.append('language', currentLanguage);
            
            if (imageFile) {
                formData.append('image', imageFile);
            }
            
            try {
                const response = await fetch('manage_trees.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeModal();
                    loadTrees();
                } else {
                    alert(translations[currentLanguage]['save-error'] + ': ' + result.error);
                }
            } catch (error) {
                console.error('Error saving tree:', error);
                alert(translations[currentLanguage]['save-error-retry']);
            }
        });

        // Close modal when clicking outside
        document.getElementById('treeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close family photo modal when clicking outside
        document.getElementById('familyPhotoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFamilyPhotoModal();
            }
        });

        // Get language from URL parameter
        function getLanguageFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const lang = urlParams.get('lang');
            return lang && ['en', 'pt', 'et', 'fr', 'de'].includes(lang) ? lang : 'en';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTrees();
            const initialLanguage = getLanguageFromUrl();
            switchLanguage(initialLanguage);
        });
    </script>
</body>
</html>