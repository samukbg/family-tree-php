<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Family Tree</title>
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
    <style>
        .photo-preview-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            align-items: center;
            justify-content: center;
        }
        .photo-preview-content {
            position: relative;
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
        }
        .photo-preview-image {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <!-- Language Toggle -->
    <div class="language-toggle">
        <button class="flag-btn active" onclick="switchLanguage('pt')" id="pt-flag" title="Português">🇧🇷</button>
        <button class="flag-btn" onclick="switchLanguage('en')" id="en-flag" title="English">🇺🇸</button>
        <button class="flag-btn" onclick="switchLanguage('de')" id="de-flag" title="Deutsch">🇩🇪</button>
        <button class="flag-btn" onclick="switchLanguage('fr')" id="fr-flag" title="Français">🇫🇷</button>
        <button class="flag-btn" onclick="switchLanguage('et')" id="et-flag" title="Eesti">🇪🇪</button>
    </div>

    <!-- Back to Trees Button -->
    <div class="fixed top-4 left-4 z-50">
        <button onclick="goBackToTrees()" class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-white/30 transition-all duration-300 flex items-center space-x-2">
            <span>←</span>
            <span data-translate="back-to-trees">Voltar às Árvores</span>
        </button>
    </div>

        <header class="text-center mb-8 md:mb-12 relative">
            <div class="header-decoration absolute inset-0 flex items-center justify-center opacity-10">
                <svg width="300" height="200" viewBox="0 0 300 200" class="text-blue-600">
                    <path d="M150 50 L50 150 L250 150 Z" fill="none" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                    <circle cx="150" cy="50" r="8" fill="currentColor" opacity="0.5"/>
                    <circle cx="50" cy="150" r="6" fill="currentColor" opacity="0.5"/>
                    <circle cx="250" cy="150" r="6" fill="currentColor" opacity="0.5"/>
                </svg>
            </div>
            
            <div class="relative z-10 mt-10">
                <div class="mb-6">
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-br from-white via-blue-100 to-purple-200 mb-2 tracking-tight leading-tight">
                        <span data-translate="title">Árvore Genealógica</span> <span id="family-name" class="editable-field" contenteditable="true" onblur="saveText('family-name', this.innerText)">...</span>
                    </h1>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-purple-500 mx-auto rounded-full shadow-lg"></div>
                </div>
                
                <div class="max-w-4xl mx-auto">
                    <p id="subtitle" class="editable-field text-lg md:text-xl text-blue-50/90 leading-relaxed font-medium tracking-wide mb-4" contenteditable="true" onblur="saveText('subtitle', this.innerText)" data-translate="subtitle">Uma representação visual das conexões familiares da carta de 1984, com idades atualizadas para 2025.</p>
                    <p class="text-sm md:text-base text-white/70 italic" data-translate="heritage">Preservando nossa história familiar através das gerações</p>
                </div>
            </div>
            
            <!-- Decorative elements -->
            <div class="absolute top-0 left-1/4 w-2 h-2 bg-white/30 rounded-full animate-pulse"></div>
            <div class="absolute top-10 right-1/3 w-1 h-1 bg-purple-300/50 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-5 left-1/3 w-1.5 h-1.5 bg-blue-300/40 rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        </header>

        <!-- View Toggle Tabs -->
        <div class="view-tabs-container">
            <div class="view-tabs">
                <button class="view-tab active" id="vertical-tab" onclick="switchView('vertical')" data-translate="vertical-view">Vertical</button>
                <button class="view-tab" id="horizontal-tab" onclick="switchView('horizontal')" data-translate="horizontal-view">Horizontal</button>
            </div>
        </div>

        <div class="tree-container">
            <div class="scroll-hint md:hidden" data-translate="scroll-hint">← Scroll horizontally to explore the family tree →</div>
            <div class="tree" id="family-tree">
                <!-- Family tree will be dynamically loaded here -->
            </div>
            <button class="empty-tree-btn" id="addFirstPersonBtn" onclick="showAddMemberModal('root', 'child')" data-translate="add-first-person">Add First Person</button>
        </div>
        
        <footer class="text-center mt-8 md:mt-12 text-white text-sm">
            <p data-translate="footer">Crafted with HTML5 & Tailwind CSS. Pan and scroll horizontally to view the entire tree.</p>
        </footer>

    <!-- Photo Menu -->
    <div class="photo-menu" id="photoMenu">
        <div class="photo-menu-option" id="uploadOption" onclick="selectUploadPhoto()">
            <span>📷</span>
            <span data-translate="upload-photo">Upload Photo</span>
        </div>
        <div class="photo-menu-option" id="viewOption" onclick="viewPhoto()">
            <span>👁️</span>
            <span data-translate="view-photo">View</span>
        </div>
        <div class="photo-menu-option delete" id="deleteOption" onclick="deletePhoto()" style="display: none;">
            <span>🗑️</span>
            <span data-translate="delete-photo">Delete Photo</span>
        </div>
    </div>

    <!-- Photo Preview Modal -->
    <div class="photo-preview-modal" id="photoPreviewModal">
        <div class="photo-preview-content">
            <button class="photo-preview-close" onclick="closePhotoPreview()">×</button>
            <img class="photo-preview-image" id="photoPreviewImage" src="" alt="Photo Preview">
        </div>
    </div>

    <!-- Add/Edit Member Modal -->
    <div class="add-member-modal" id="addMemberModal">
        <div class="add-member-form">
            <h2 class="form-title" id="formTitle" data-translate="add-member-title">Add New Family Member</h2>
            <form id="addMemberForm">
                <input type="hidden" id="formAction" value="add">
                <div class="form-group">
                    <label class="form-label" data-translate="name-label">Name:</label>
                    <input type="text" class="form-input" id="memberName" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" data-translate="details-label">Details (Birth date, profession, etc.):</label>
                    <textarea class="form-textarea" id="memberDetails" rows="4" placeholder="pt: (Texto em Português)&#10;en: (Text in English)&#10;de: (Text in German)&#10;et: (Tekst eesti keeles)"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" data-translate="status-label">Status:</label>
                    <select class="form-select" id="memberStatus" onchange="toggleDeceasedDate(this.value)">
                        <option value="alive" data-translate="alive-option">Alive</option>
                        <option value="deceased" data-translate="deceased-option">Deceased</option>
                    </select>
                </div>

                <div class="form-group" id="birthDateGroup">
                    <label class="form-label" data-translate="birth-date-label">Born in:</label>
                    <input type="text" class="form-input" id="birthDate">
                </div>

                <div class="form-group" id="deceasedDateGroup" style="display: none;">
                    <label class="form-label" data-translate="deceased-date-label">Date of Death:</label>
                    <input type="text" class="form-input" id="deceasedDate">
                </div>
                
                <div class="form-group" id="relationshipGroup">
                    <label class="form-label" data-translate="relationship-label">Relationship to selected person:</label>
                    <select class="form-select" id="relationship" required>
                        <option value="" data-translate="select-relationship">Select relationship...</option>
                        <option value="child" data-translate="child-option">Child</option>
                        <option value="spouse" data-translate="spouse-option">Spouse</option>
                        <option value="parent" data-translate="parent-option">Parent</option>
                        <option value="sibling" data-translate="sibling-option">Sibling</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" onclick="closeAddMemberModal()" data-translate="cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" data-translate="add-btn">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let familyTreeData = null;
        let siteData = {};
        let currentSelectedMember = null;

        // Translation data
        const translations = {
            en: {
                title: "Family Tree",
                "back-to-trees": "Back to Trees",
                "scroll-hint": "← Scroll horizontally to explore the family tree →",
                deceased: "Deceased",
                daughter: "Daughter",
                "letter-author": "Author of the letter",
                "denise-spouse": "Spouse",
                "vertical-view": "Vertical",
                "horizontal-view": "Horizontal",
                "upload-photo": "Upload Photo",
                "view-photo": "View",
                "delete-photo": "Delete Photo",
                "heritage": "Preserving our family history through generations",
                footer: "Pan and scroll horizontally to view the entire tree.",
                "add-member-title": "Add New Family Member",
                "name-label": "Name",
                "details-label": "Details (Birth date, profession, etc.)",
                "status-label": "Status",
                "alive-option": "Alive",
                "deceased-option": "Deceased",
                "relationship-label": "Relationship to selected person",
                "select-relationship": "Select relationship...",
                "child-option": "Child",
                "spouse-option": "Spouse",
                "parent-option": "Parent",
                "sibling-option": "Sibling",
                "cancel-btn": "Cancel",
                "add-btn": "Add Member",
                "birth-date-label": "Born in:",
                "add-first-person": "Add First Person",
                "edit-person-title": "Edit",
                "save-changes-btn": "Save Changes",
                "delete-person-confirm": "Are you sure you want to delete",
                "delete-person-warning": "This action cannot be undone.",
                "delete-person-error": "Failed to delete person",
                "delete-person-error-general": "An error occurred while deleting the person.",
                "required-fields-error": "Please fill in all required fields.",
                "action-member-error": "Failed to",
                "action-member-error-general": "An error occurred while",
                "upload-photo-error": "Failed to upload photo",
                "upload-photo-error-general": "Error uploading photo. Please try again.",
                "delete-photo-confirm": "Delete photo for",
                "delete-photo-error": "Failed to delete photo",
                "delete-photo-error-general": "Error deleting photo. Please try again.",
                "no-photo-to-delete": "No photo to delete for this person.",
                "tree-not-found": "Tree not found. Redirecting to trees page.",
                "edit-member-title": "Edit"
            },
            pt: {
                title: "Árvore Genealógica",
                "back-to-trees": "Voltar às Árvores",
                "scroll-hint": "← Role horizontalmente para explorar a árvore genealógica →",
                deceased: "Falecido",
                daughter: "Filha",
                "letter-author": "Autora da carta",
                "denise-spouse": "Cônjuge",
                "vertical-view": "Vertical",
                "horizontal-view": "Horizontal",
                "upload-photo": "Carregar Foto",
                "view-photo": "Visualizar",
                "delete-photo": "Excluir Foto",
                "heritage": "Preservando nossa história familiar através das gerações",
                footer: "Deslize e role horizontalmente para visualizar toda a árvore.",
                "add-member-title": "Adicionar Novo Membro da Família",
                "name-label": "Nome",
                "details-label": "Detalhes (Data de nascimento, profissão, etc.)",
                "status-label": "Status",
                "alive-option": "Vivo",
                "deceased-option": "Falecido",
                "relationship-label": "Relacionamento com a pessoa selecionada",
                "select-relationship": "Selecione o relacionamento...",
                "child-option": "Filho/Filha",
                "spouse-option": "Cônjuge",
                "parent-option": "Pai/Mãe",
                "sibling-option": "Irmão/Irmã",
                "cancel-btn": "Cancelar",
                "add-btn": "Adicionar Membro",
                "birth-date-label": "Nascido em:",
                "add-first-person": "Adicionar Primeira Pessoa",
                "edit-person-title": "Editar",
                "save-changes-btn": "Salvar Alterações",
                "delete-person-confirm": "Tem certeza que deseja excluir",
                "delete-person-warning": "Esta ação não pode ser desfeita.",
                "delete-person-error": "Falha ao excluir pessoa",
                "delete-person-error-general": "Ocorreu um erro ao excluir a pessoa.",
                "required-fields-error": "Por favor, preencha todos os campos obrigatórios.",
                "action-member-error": "Falha ao",
                "action-member-error-general": "Ocorreu um erro ao",
                "upload-photo-error": "Falha ao carregar foto",
                "upload-photo-error-general": "Erro ao carregar foto. Tente novamente.",
                "delete-photo-confirm": "Excluir foto de",
                "delete-photo-error": "Falha ao excluir foto",
                "delete-photo-error-general": "Erro ao excluir foto. Tente novamente.",
                "no-photo-to-delete": "Nenhuma foto para excluir para esta pessoa.",
                "tree-not-found": "Árvore não encontrada. Redirecionando para a página de árvores.",
                "edit-member-title": "Editar"
            },
            de: {
                title: "Stammbaum",
                "back-to-trees": "Zurück zu Bäumen",
                "scroll-hint": "← Horizontal scrollen, um den Stammbaum zu erkunden →",
                deceased: "Verstorben",
                daughter: "Tochter",
                "letter-author": "Verfasserin des Briefes",
                "denise-spouse": "Ehepartner",
                "vertical-view": "Vertikal",
                "horizontal-view": "Horizontal",
                "upload-photo": "Foto Hochladen",
                "view-photo": "Ansehen",
                "delete-photo": "Foto Löschen",
                "heritage": "Unsere Familiengeschichte für kommende Generationen bewahren",
                footer: "Schwenken und scrollen Sie horizontal, um den gesamten Baum anzuzeigen.",
                "add-member-title": "Neues Familienmitglied hinzufügen",
                "name-label": "Name",
                "details-label": "Details (Geburtsdatum, Beruf, etc.)",
                "status-label": "Status",
                "alive-option": "Lebend",
                "deceased-option": "Verstorben",
                "relationship-label": "Beziehung zur ausgewählten Person",
                "select-relationship": "Beziehung wählen...",
                "child-option": "Kind",
                "spouse-option": "Ehepartner",
                "parent-option": "Elternteil",
                "sibling-option": "Geschwister",
                "cancel-btn": "Abbrechen",
                "add-btn": "Mitglied hinzufügen",
                "birth-date-label": "Geboren in:",
                "add-first-person": "Erste Person hinzufügen",
                "edit-person-title": "Bearbeiten",
                "save-changes-btn": "Änderungen speichern",
                "delete-person-confirm": "Sind Sie sicher, dass Sie löschen möchten",
                "delete-person-warning": "Diese Aktion kann nicht rückgängig gemacht werden.",
                "delete-person-error": "Person löschen fehlgeschlagen",
                "delete-person-error-general": "Beim Löschen der Person ist ein Fehler aufgetreten.",
                "required-fields-error": "Bitte füllen Sie alle erforderlichen Felder aus.",
                "action-member-error": "Fehler bei",
                "action-member-error-general": "Ein Fehler ist aufgetreten bei",
                "upload-photo-error": "Foto hochladen fehlgeschlagen",
                "upload-photo-error-general": "Fehler beim Hochladen des Fotos. Bitte versuchen Sie es erneut.",
                "delete-photo-confirm": "Foto löschen für",
                "delete-photo-error": "Foto löschen fehlgeschlagen",
                "delete-photo-error-general": "Fehler beim Löschen des Fotos. Bitte versuchen Sie es erneut.",
                "no-photo-to-delete": "Kein Foto zum Löschen für diese Person.",
                "tree-not-found": "Baum nicht gefunden. Weiterleitung zur Baumseite.",
                "edit-member-title": "Bearbeiten"
            },
            fr: {
                title: "Arbre Généalogique",
                "back-to-trees": "Retour aux Arbres",
                "scroll-hint": "← Faites défiler horizontalement pour explorer l'arbre généalogique →",
                deceased: "Décédé",
                daughter: "Fille",
                "letter-author": "Auteur de la lettre",
                "denise-spouse": "Conjoint",
                "vertical-view": "Vertical",
                "horizontal-view": "Horizontal",
                "upload-photo": "Télécharger une Photo",
                "view-photo": "Voir",
                "delete-photo": "Supprimer la Photo",
                "heritage": "Préserver notre histoire familiale à travers les générations",
                footer: "Faites glisser et défiler horizontalement pour voir tout l'arbre.",
                "add-member-title": "Ajouter un Nouveau Membre de la Famille",
                "name-label": "Nom",
                "details-label": "Détails (Date de naissance, profession, etc.)",
                "status-label": "Statut",
                "alive-option": "Vivant",
                "deceased-option": "Décédé",
                "relationship-label": "Relation avec la personne sélectionnée",
                "select-relationship": "Sélectionner une relation...",
                "child-option": "Enfant",
                "spouse-option": "Conjoint",
                "parent-option": "Parent",
                "sibling-option": "Frère/Sœur",
                "cancel-btn": "Annuler",
                "add-btn": "Ajouter un Membre",
                "birth-date-label": "Né en:",
                "add-first-person": "Ajouter la Première Personne",
                "edit-person-title": "Modifier",
                "save-changes-btn": "Enregistrer les Modifications",
                "delete-person-confirm": "Êtes-vous sûr de vouloir supprimer",
                "delete-person-warning": "Cette action ne peut pas être annulée.",
                "delete-person-error": "Échec de la suppression de la personne",
                "delete-person-error-general": "Une erreur s'est produite lors de la suppression de la personne.",
                "required-fields-error": "Veuillez remplir tous les champs obligatoires.",
                "action-member-error": "Échec de",
                "action-member-error-general": "Une erreur s'est produite lors de",
                "upload-photo-error": "Échec du téléchargement de la photo",
                "upload-photo-error-general": "Erreur lors du téléchargement de la photo. Veuillez réessayer.",
                "delete-photo-confirm": "Supprimer la photo de",
                "delete-photo-error": "Échec de la suppression de la photo",
                "delete-photo-error-general": "Erreur lors de la suppression de la photo. Veuillez réessayer.",
                "no-photo-to-delete": "Aucune photo à supprimer pour cette personne.",
                "tree-not-found": "Arbre non trouvé. Redirection vers la page des arbres.",
                "edit-member-title": "Modifier"
            },
            et: {
                title: "Sugupuu",
                "back-to-trees": "Tagasi puude juurde",
                "scroll-hint": "← Keri horisontaalselt, et sugupuud uurida →",
                deceased: "Surnud",
                daughter: "Tütar",
                "letter-author": "Kirja autor",
                "denise-spouse": "Abikaasa",
                "vertical-view": "Vertikaalne",
                "horizontal-view": "Horisontaalne",
                "upload-photo": "Laadi foto üles",
                "view-photo": "Vaata",
                "delete-photo": "Kustuta foto",
                "heritage": "Perekonna ajaloo säilitamine põlvkondade kaupa",
                footer: "Liiguta ja keri horisontaalselt, et näha kogu puud.",
                "add-member-title": "Lisa uus pereliige",
                "name-label": "Nimi",
                "details-label": "Üksikasjad (sünniaeg, amet jne)",
                "status-label": "Staatus",
                "alive-option": "Elus",
                "deceased-option": "Surnud",
                "relationship-label": "Suhe valitud isikuga",
                "select-relationship": "Vali suhe...",
                "child-option": "Laps",
                "spouse-option": "Abikaasa",
                "parent-option": "Vanem",
                "sibling-option": "Õde/vend",
                "cancel-btn": "Tühista",
                "add-btn": "Lisa liige",
                "birth-date-label": "Sündinud:",
                "deceased-date-label": "Surma kuupäev:",
                "add-first-person": "Lisa esimene isik",
                "edit-person-title": "Muuda",
                "save-changes-btn": "Salvesta muudatused",
                "delete-person-confirm": "Kas oled kindel, et soovid kustutada",
                "delete-person-warning": "Seda tegevust ei saa tagasi võtta.",
                "delete-person-error": "Isiku kustutamine ebaõnnestus",
                "delete-person-error-general": "Isiku kustutamisel tekkis viga.",
                "required-fields-error": "Palun täida kõik nõutud väljad.",
                "action-member-error": "Liikme tegevus ebaõnnestus",
                "action-member-error-general": "Liikme toimingul tekkis viga.",
                "upload-photo-error": "Foto üleslaadimine ebaõnnestus",
                "upload-photo-error-general": "Foto üleslaadimisel tekkis viga. Palun proovi uuesti.",
                "delete-photo-confirm": "Kustuta foto kasutajale",
                "delete-photo-error": "Foto kustutamine ebaõnnestus",
                "delete-photo-error-general": "Foto kustutamisel tekkis viga. Palun proovi uuesti.",
                "no-photo-to-delete": "Sellel isikul pole fotot kustutamiseks.",
                "tree-not-found": "Puud ei leitud. Suunatakse puude lehele.",
                "edit-member-title": "Muuda"
            }
        };

        let currentLanguage = 'pt';
        let currentView = 'vertical';
        let currentTreeId = null;
        let currentTreeData = null;

        function switchView(view) {
            const treeContainer = document.querySelector('.tree-container');
            const tree = document.querySelector('.tree');
            const verticalTab = document.getElementById('vertical-tab');
            const horizontalTab = document.getElementById('horizontal-tab');
            
            currentView = view;
            
            // Update tab states
            verticalTab.classList.remove('active');
            horizontalTab.classList.remove('active');
            
            if (view === 'horizontal') {
                horizontalTab.classList.add('active');
                treeContainer.classList.add('horizontal');
                tree.classList.add('horizontal');
            } else {
                verticalTab.classList.add('active');
                treeContainer.classList.remove('horizontal');
                tree.classList.remove('horizontal');
            }
        }

        function isMobileDevice() {
            return window.innerWidth <= 768;
        }

        function setDefaultView() {
            switchView('vertical');
        }

        function switchLanguage(lang) {
            const content = document.querySelector('body');
            content.classList.add('loading');
            
            setTimeout(() => {
                currentLanguage = lang;
                document.documentElement.lang = lang;
                
                // Update active flag
                document.querySelectorAll('.flag-btn').forEach(btn => btn.classList.remove('active'));
                const currentFlag = document.getElementById(lang + '-flag');
                if (currentFlag) {
                    currentFlag.classList.add('active');
                }
                
                // Update all static translatable elements
                document.querySelectorAll('[data-translate]').forEach(element => {
                    const key = element.getAttribute('data-translate');
                    if (translations[lang] && translations[lang][key]) {
                        element.innerHTML = translations[lang][key];
                    }
                });

                // Restore saved text after translation
                loadSiteData();

                // Update dynamic person details
                document.querySelectorAll('.tree-card').forEach(card => {
                    const detailsEl = card.querySelector('.person-details');
                    if (detailsEl && card.dataset.personDetails) {
                        try {
                            const detailsObj = JSON.parse(unescape(card.dataset.personDetails));
                            detailsEl.textContent = getTranslatedDetail(detailsObj);
                        } catch (e) {
                            console.error('Could not parse person details for translation', e);
                        }
                    }
                });
                
                content.classList.remove('loading');
            }, 150);
        }

        // Get current tree ID from URL parameters
        function getCurrentTreeId() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('tree');
        }

        // Get data file name for current tree
        function getDataFileName() {
            if (!currentTreeData) return 'data.json'; // fallback to original
            return currentTreeData.dataFile || 'data.json';
        }

        // Get site data file name for current tree
        function getSiteDataFileName() {
            if (!currentTreeData) return 'site_data.json'; // fallback to original
            return currentTreeData.siteDataFile || 'site_data.json';
        }

        // Load current tree metadata
        async function loadCurrentTreeData() {
            currentTreeId = getCurrentTreeId();
            if (!currentTreeId) {
                // Redirect to trees page if no tree is selected
                window.location.href = 'trees.php';
                return;
            }

            try {
                const response = await fetch('trees.json?v=' + Date.now());
                const treesData = await response.json();
                const trees = treesData.trees || [];
                
                currentTreeData = trees.find(tree => tree.id === currentTreeId);
                if (!currentTreeData) {
                    alert(translations[currentLanguage]['tree-not-found']);
                    window.location.href = 'trees.php';
                    return;
                }
            } catch (error) {
                console.error('Error loading trees data:', error);
                window.location.href = 'trees.php';
            }
        }

        // Load family tree data from JSON
        async function loadFamilyTreeData() {
            try {
                const dataFile = getDataFileName();
                const response = await fetch(`${dataFile}?v=` + Date.now());
                const data = await response.json();
                if (data && data.familyTree) {
                    familyTreeData = data.familyTree;
                    renderFamilyTree(familyTreeData);
                    document.getElementById('addFirstPersonBtn').style.display = 'none';
                    setTimeout(loadStoredPhotos, 100);
                } else {
                    familyTreeData = null;
                    document.getElementById('addFirstPersonBtn').style.display = 'block';
                    document.getElementById('family-tree').innerHTML = '';
                }
            } catch (error) {
                familyTreeData = null;
                document.getElementById('addFirstPersonBtn').style.display = 'block';
                document.getElementById('family-tree').innerHTML = '';
                console.error('Error loading family tree data:', error);
            }
        }

        // Render family tree from JSON data
        function renderFamilyTree(person) {
            const treeContainer = document.getElementById('family-tree');
            treeContainer.innerHTML = generatePersonHTML(person, true);
        }

        // Generate HTML for a person and their descendants
        function generatePersonHTML(person, isRoot = false) {
            if (!person) return '';
            
            if (isRoot) {
                // Root level needs the full <ul><li> wrapper
                let html = '<ul>';
                html += '<li>';
                html += generatePersonCard(person);
                if (person.spouse) {
                    // For root with spouse, wrap in couple-container
                    html = '<ul><li><div class="couple-container">';
                    html += generatePersonCard(person);
                    html += generatePersonCard(person.spouse, true);
                    html += '</div>';
                }
                
                // Generate children recursively
                if (person.children && person.children.length > 0) {
                    html += generateChildrenHTML(person.children);
                }
                
                html += '</li></ul>';
                return html;
            } else {
                // For non-root, just generate the person card and children
                let html = '';
                if (person.spouse) {
                    html += '<div class="couple-container">';
                    html += generatePersonCard(person);
                    html += generatePersonCard(person.spouse, true);
                    html += '</div>';
                } else {
                    html += generatePersonCard(person);
                }
                
                // Generate children recursively
                if (person.children && person.children.length > 0) {
                    html += generateChildrenHTML(person.children);
                }
                
                return html;
            }
        }
        
        // Function to sort children by birth date (oldest first)
        function sortChildrenByBirthDate(children) {
            return children.sort((a, b) => {
                let birthA = null;
                let birthB = null;
                
                // Extract birth year from birthDate
                if (a.birthDate) {
                    const matches = a.birthDate.match(/\d{4}/);
                    if (matches) {
                        birthA = parseInt(matches[0]);
                    }
                }
                
                if (b.birthDate) {
                    const matches = b.birthDate.match(/\d{4}/);
                    if (matches) {
                        birthB = parseInt(matches[0]);
                    }
                }
                
                // If no birthDate, try to extract from details
                if (birthA === null && a.details && typeof a.details === 'object') {
                    for (const detail of Object.values(a.details)) {
                        const matches = detail.match(/\b(19|20)\d{2}\b/);
                        if (matches) {
                            birthA = parseInt(matches[0]);
                            break;
                        }
                    }
                }
                
                if (birthB === null && b.details && typeof b.details === 'object') {
                    for (const detail of Object.values(b.details)) {
                        const matches = detail.match(/\b(19|20)\d{2}\b/);
                        if (matches) {
                            birthB = parseInt(matches[0]);
                            break;
                        }
                    }
                }
                
                // If both have birth dates, sort by birth year (oldest first)
                if (birthA !== null && birthB !== null) {
                    return birthA - birthB;
                }
                
                // If only one has a birth date, put it first
                if (birthA !== null) return -1;
                if (birthB !== null) return 1;
                
                // If neither has a birth date, maintain current order
                return 0;
            });
        }

        // Generate HTML for children list
        function generateChildrenHTML(children) {
            if (!children || children.length === 0) return '';
            
            // Sort children by birth date before rendering
            const sortedChildren = sortChildrenByBirthDate([...children]);
            
            let html = '<ul>';
            sortedChildren.forEach(child => {
                html += '<li>';
                if (child.spouse) {
                    html += '<div class="couple-container">';
                    html += generatePersonCard(child);
                    html += generatePersonCard(child.spouse, true);
                    html += '</div>';
                } else {
                    html += generatePersonCard(child);
                }
                
                // Recursively generate grandchildren
                if (child.children && child.children.length > 0) {
                    html += generateChildrenHTML(child.children);
                }
                html += '</li>';
            });
            html += '</ul>';
            
            return html;
        }

        // Generate HTML for a single person card
        function generatePersonCard(person, isSpouse = false) {
            let classes = 'tree-card';
            if (person.status === 'deceased') classes += ' deceased';
            if (isSpouse) classes += ' spouse';
            if (person.special === 'author') classes += ' author';
            if (person.special === 'recipient') classes += ' recipient';
            
            let personId = person.id || generateId();
            
            let detailsString = '';
            if (person.details && typeof person.details === 'object') {
                detailsString = `pt: ${person.details.pt || ''}\nen: ${person.details.en || ''}\nde: ${person.details.de || ''}\net: ${person.details.et || ''}`;
            }

            let cardHtml = `<div class="${classes}" 
                                 data-person-id="${personId}" 
                                 data-person-name="${person.name}" 
                                 data-person-details="${escape(JSON.stringify(person.details))}"
                                 data-person-status="${person.status || 'alive'}"
                                 data-birth-date="${person.birthDate || ''}"
                                 data-deceased-date="${person.deceasedDate || ''}">`;
            
            cardHtml += `<div class="person-photo" onclick="uploadPhoto(this)"></div>`;
            
            cardHtml += `<p class="person-name">${person.name}</p>`;
            cardHtml += '<div class="life-events">';
            if (person.birthDate) {
                cardHtml += `<span class="birth-info">⭐ ${person.birthDate}</span>`;
            }
            if (person.status === 'deceased' && person.deceasedDate) {
                cardHtml += `<span class="deceased-info">✝ ${person.deceasedDate}</span>`;
            }
            cardHtml += '</div>';
            
            if (person.details) {
                cardHtml += `<p class="person-details">${getTranslatedDetail(person.details)}</p>`;
            }

            // Action Buttons
            cardHtml += `<button class="person-action-btn add-spouse-btn" onclick="showAddMemberModal('${personId}', 'spouse')" title="Add Spouse">+</button>`;
            cardHtml += `<button class="person-action-btn delete-person-btn" onclick="deletePerson('${personId}')" title="Delete Person">-</button>`;
            cardHtml += `<button class="person-action-btn edit-person-btn" onclick="editPerson('${personId}')" title="Edit Person">✏️</button>`;

            cardHtml += '</div>';

            return cardHtml;
        }

        // Get translated detail text
        function getTranslatedDetail(details) {
            if (typeof details === 'object' && details !== null) {
                return details[currentLanguage] || details['en'] || '';
            }
            return details; // Should not happen with new structure, but good fallback
        }

        // Generate a unique ID
        function generateId(name) {
            return 'person_' + name.replace(/\s+/g, '-').toLowerCase() + '_' + Math.random().toString(36).substr(2, 9);
        }

        // Show add member modal
        function showAddMemberModal(personId, relationshipType = 'child') {
            document.getElementById('addMemberForm').reset();
            document.getElementById('formAction').value = 'add';
            
            document.getElementById('formTitle').setAttribute('data-translate', 'add-member-title');
            document.getElementById('formTitle').textContent = translations[currentLanguage]['add-member-title'];
            
            document.getElementById('submitBtn').setAttribute('data-translate', 'add-btn');
            document.getElementById('submitBtn').textContent = translations[currentLanguage]['add-btn'];

            if (familyTreeData) {
                document.getElementById('relationshipGroup').style.display = 'block';
                document.getElementById('relationship').setAttribute('required', 'required');
                document.getElementById('relationship').value = relationshipType;
            } else {
                document.getElementById('relationshipGroup').style.display = 'none';
                document.getElementById('relationship').removeAttribute('required');
            }
            
            currentSelectedMember = personId;
            document.getElementById('addMemberModal').style.display = 'flex';
        }

        // Show edit member modal
        function editPerson(personId) {
            const personCard = document.querySelector(`[data-person-id="${personId}"]`);
            if (!personCard) return;

            document.getElementById('addMemberForm').reset();
            document.getElementById('formAction').value = 'edit';

            const details = JSON.parse(unescape(personCard.dataset.personDetails));
            let detailsText = '';
            if (details) {
                detailsText = `pt: ${details.pt || ''}\nen: ${details.en || ''}\nde: ${details.de || ''}\net: ${details.et || ''}`;
            }

            document.getElementById('memberName').value = personCard.dataset.personName;
            document.getElementById('memberDetails').value = detailsText;
            document.getElementById('memberStatus').value = personCard.dataset.personStatus;
            
            toggleDeceasedDate(personCard.dataset.personStatus);
            if (personCard.dataset.personStatus === 'deceased') {
                document.getElementById('deceasedDate').value = personCard.dataset.deceasedDate || '';
            } else {
                document.getElementById('birthDate').value = personCard.dataset.birthDate || '';
            }

            document.getElementById('formTitle').setAttribute('data-translate', 'edit-member-title');
            document.getElementById('formTitle').textContent = translations[currentLanguage]['edit-member-title'] + ' ' + personCard.dataset.personName;
            
            document.getElementById('submitBtn').setAttribute('data-translate', 'save-changes-btn');
            document.getElementById('submitBtn').textContent = translations[currentLanguage]['save-changes-btn'];

            document.getElementById('relationshipGroup').style.display = 'none';
            document.getElementById('relationship').removeAttribute('required');

            currentSelectedMember = personId;
            document.getElementById('addMemberModal').style.display = 'flex';
        }

        // Close add member modal
        function closeAddMemberModal() {
            document.getElementById('addMemberModal').style.display = 'none';
            document.getElementById('addMemberForm').reset();
            document.getElementById('relationship').setAttribute('required', 'required');
            currentSelectedMember = null;
        }

        // Delete a person
        async function deletePerson(personId) {
            const personName = document.querySelector(`[data-person-id="${personId}"]`).dataset.personName;
            if (confirm(`${translations[currentLanguage]['delete-person-confirm']} ${personName}? ${translations[currentLanguage]['delete-person-warning']}`)) {
                try {
                    const response = await fetch('delete_person.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: personId, tree: currentTreeId })
                    });
                    const result = await response.json();
                    if (result.success) {
                        await loadFamilyTreeData();
                    } else {
                        alert(translations[currentLanguage]['delete-person-error'] + ': ' + result.error);
                    }
                } catch (error) {
                    console.error('Error deleting person:', error);
                    alert(translations[currentLanguage]['delete-person-error-general']);
                }
            }
        }

        async function loadSiteData() {
            try {
                const siteDataFile = getSiteDataFileName();
                const response = await fetch(`${siteDataFile}?v=` + Date.now());
                siteData = await response.json();
                if (siteData['family-name']) {
                    document.getElementById('family-name').textContent = siteData['family-name'];
                }
                if (siteData.subtitle && siteData.subtitle[currentLanguage]) {
                    document.getElementById('subtitle').textContent = siteData.subtitle[currentLanguage];
                }
            } catch (e) {
                console.log('Site data file not found, using default text.');
            }
        }

        async function saveText(key, value) {
            if (key === 'subtitle') {
                if (!siteData.subtitle) siteData.subtitle = {};
                siteData.subtitle[currentLanguage] = value;
            } else {
                siteData[key] = value;
            }
            try {
                siteData.tree = currentTreeId; // Add tree ID to the data
                const response = await fetch('update_text.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(siteData)
                });
                const result = await response.json();
                if (!result.success) {
                    console.error('Failed to save text:', result.error);
                }
            } catch (e) {
                console.error('Error saving text:', e);
            }
        }

        function goBackToTrees() {
            window.location.href = 'trees.php';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for mobile users
            const treeContainer = document.querySelector('.tree-container');
            let isScrolling = false;
            
            treeContainer.addEventListener('scroll', function() {
                if (!isScrolling) {
                    window.requestAnimationFrame(function() {
                        // Add any scroll-based animations here if needed
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            });

            // Initialize with Portuguese and set default view
            setDefaultView();
            
            // Load current tree data first, then load family tree
            loadCurrentTreeData().then(() => {
                loadFamilyTreeData();
                switchLanguage('pt');
            });
            
            // Handle window resize to adjust default view
            window.addEventListener('resize', function() {
                // No automatic view switching on resize
            });
            
            // Add/Edit member form handler
            document.getElementById('addMemberForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const action = document.getElementById('formAction').value;
                const name = document.getElementById('memberName').value;
                const detailsText = document.getElementById('memberDetails').value;
                const status = document.getElementById('memberStatus').value;
                const relationship = document.getElementById('relationship').value;
                const deceasedDate = document.getElementById('deceasedDate').value;
                const birthDate = document.getElementById('birthDate').value;

                // Parse details
                const details = { pt: '', en: '', de: '', et: '' };
                detailsText.split('\n').forEach(line => {
                    if (line.startsWith('pt:')) details.pt = line.substring(3).trim();
                    if (line.startsWith('en:')) details.en = line.substring(3).trim();
                    if (line.startsWith('de:')) details.de = line.substring(3).trim();
                    if (line.startsWith('et:')) details.et = line.substring(3).trim();
                });

                let data = {
                    name,
                    details,
                    status,
                    id: currentSelectedMember,
                    tree: currentTreeId
                };

                data.birthDate = birthDate;
                if (status === 'deceased') {
                    data.deceasedDate = deceasedDate;
                }
                
                let endpoint = '';

                if (action === 'edit') {
                    endpoint = 'edit_person.php';
                } else {
                    endpoint = 'add_person.php';
                    data.relationship = relationship;
                    data.parentId = currentSelectedMember;
                }

                if (!name || (action === 'add' && familyTreeData && !relationship)) {
                    alert(translations[currentLanguage]['required-fields-error']);
                    return;
                }

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        closeAddMemberModal();
                        await loadFamilyTreeData();
                    } else {
                        alert(`${translations[currentLanguage]['action-member-error']} ${action} member: ` + result.error);
                    }
                } catch (error) {
                    console.error(`Error ${action}ing member:`, error);
                    alert(`${translations[currentLanguage]['action-member-error-general']} ${action}ing the member.`);
                }
            });
            
            // Close modal when clicking outside
            document.getElementById('addMemberModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAddMemberModal();
                }
            });

            document.getElementById('photoPreviewModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closePhotoPreview();
                }
            });
        });

        // Add touch support for better mobile experience
        let startX = null;
        let startY = null;
        
        document.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchmove', e => {
            if (!startX || !startY) return;
            
            let diffX = startX - e.touches[0].clientX;
            let diffY = startY - e.touches[0].clientY;
            
            // If horizontal scroll is more prominent, prevent vertical scroll
            if (Math.abs(diffX) > Math.abs(diffY)) {
                e.preventDefault();
            }
        });

        let currentPhotoElement = null;

        function uploadPhoto(photoElement) {
            currentPhotoElement = photoElement;
            
            // Check if person has a photo to show/hide delete option
            const personName = getCleanPersonName(photoElement);
            const hasPhoto = window.photoMappings && window.photoMappings[personName];
            
            document.getElementById('deleteOption').style.display = hasPhoto ? 'block' : 'none';
            
            // Position and show menu
            const rect = photoElement.getBoundingClientRect();
            const menu = document.getElementById('photoMenu');
            menu.style.left = (rect.left + rect.width / 2 - 75) + 'px';
            menu.style.top = (rect.bottom + 10) + 'px';
            menu.style.display = 'block';
        }

        function selectUploadPhoto() {
            hidePhotoMenu();
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png';
            input.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    uploadOriginalImage(file, currentPhotoElement);
                }
            };
            input.click();
        }

        function uploadOriginalImage(file, photoElement) {
            const personName = getCleanPersonName(photoElement);
            const filename = file.name;
            uploadImageToServer(file, filename, photoElement, personName);
        }

        // Server-side image upload
        async function uploadImageToServer(blob, filename, photoElement, personName) {
            const formData = new FormData();
            formData.append('image', blob, filename);
            formData.append('personName', personName);
            formData.append('filename', filename);

            try {
                const response = await fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Success - apply image and update UI
                    const webpFilename = result.filename;
                    photoElement.style.backgroundImage = `url("./images/${webpFilename}")`;
                    photoElement.classList.add('has-image');
                    photoElement.textContent = '';
                    
                    console.log(`Photo uploaded successfully: ${webpFilename}`);
                    
                    // Refresh the photo mappings
                    await loadPhotoMappings();
                } else {
                    console.error('Upload failed:', result.error);
                    alert(translations[currentLanguage]['upload-photo-error'] + ': ' + result.error);
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert(translations[currentLanguage]['upload-photo-error-general']);
            }
        }

        // Load photo mappings from server
        async function loadPhotoMappings() {
            try {
                const response = await fetch('get_photos.php');
                const photos = await response.json();
                
                if (photos.success) {
                    // Store the photo mappings locally for quick access
                    window.photoMappings = photos.data;
                    return photos.data;
                } else {
                    console.error('Failed to load photo mappings:', photos.error);
                    return {};
                }
            } catch (error) {
                console.error('Error loading photo mappings:', error);
                return {};
            }
        }

        function viewPhoto() {
            hidePhotoMenu();
            const personName = getCleanPersonName(currentPhotoElement);
            const filename = window.photoMappings && window.photoMappings[personName];
            
            if (filename) {
                // Try to load and display the image from server
                const img = new Image();
                img.onload = function() {
                    document.getElementById('photoPreviewImage').src = `./images/${filename}`;
                    document.getElementById('photoPreviewModal').style.display = 'flex';
                };
                img.onerror = function() {
                    console.warn(`Image not found: ./images/${filename}`);
                    showInitialsPreview();
                };
                img.src = `./images/${filename}`;
            } else {
                // If no photo, show initials in a modal-like view
                showInitialsPreview();
            }
        }

        function showInitialsPreview() {
            const canvas = document.createElement('canvas');
            canvas.width = 300;
            canvas.height = 300;
            const ctx = canvas.getContext('2d');
            
            // Create a circle with initials
            ctx.fillStyle = '#e0f2fe';
            ctx.beginPath();
            ctx.arc(150, 150, 150, 0, 2 * Math.PI);
            ctx.fill();
            
            ctx.fillStyle = '#0369a1';
            ctx.font = 'bold 80px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const nameElement = currentPhotoElement.closest('.tree-card').querySelector('.person-name');
            const name = nameElement.textContent.split('[')[0].trim().replace(/\(|\)/g, '');
            const initials = name.split(' ').map(n => n[0]).join('');
            ctx.fillText(initials, 150, 150);
            
            document.getElementById('photoPreviewImage').src = canvas.toDataURL();
            document.getElementById('photoPreviewModal').style.display = 'flex';
        }

        function getCleanPersonName(photoElement) {
            const nameElement = photoElement.closest('.tree-card').querySelector('.person-name');
            // Clean the name by removing brackets, deceased markers, and extra whitespace
            return nameElement.textContent
                .replace(/\[.*?\]/g, '') // Remove [Deceased] etc.
                .replace(/\(.*?\)/g, '') // Remove (Daughter) etc.
                .replace(/\s+/g, ' ')    // Normalize whitespace
                .trim();
        }

        async function deletePhoto() {
            hidePhotoMenu();
            const personName = getCleanPersonName(currentPhotoElement);
            const filename = window.photoMappings && window.photoMappings[personName];
            
            if (filename) {
                const confirmDelete = confirm(`${translations[currentLanguage]['delete-photo-confirm']} ${personName}?`);
                if (confirmDelete) {
                    try {
                        const response = await fetch('delete_photo.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                personName: personName
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Reset photo element
                            currentPhotoElement.style.backgroundImage = '';
                            currentPhotoElement.classList.remove('has-image');
                            setInitialsForElement(
                                currentPhotoElement.closest('.tree-card').querySelector('.person-name'),
                                currentPhotoElement
                            );
                            
                            // Refresh photo mappings
                            await loadPhotoMappings();
                            
                            console.log(`Photo deleted successfully: ${filename}`);
                        } else {
                            console.error('Delete failed:', result.error);
                            alert(translations[currentLanguage]['delete-photo-error'] + ': ' + result.error);
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert(translations[currentLanguage]['delete-photo-error-general']);
                    }
                }
            } else {
                alert(translations[currentLanguage]['no-photo-to-delete']);
            }
        }

        function hidePhotoMenu() {
            document.getElementById('photoMenu').style.display = 'none';
        }

        function closePhotoPreview() {
            document.getElementById('photoPreviewModal').style.display = 'none';
        }

        // Hide menu when clicking elsewhere
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('photoMenu');
            const photoElements = document.querySelectorAll('.person-photo');
            let clickedPhoto = false;
            
            photoElements.forEach(photo => {
                if (photo.contains(event.target)) {
                    clickedPhoto = true;
                }
            });
            
            if (!menu.contains(event.target) && !clickedPhoto) {
                hidePhotoMenu();
            }
        });

        async function loadStoredPhotos() {
            // Load photo mappings from server
            const photoMappings = await loadPhotoMappings();
            
            document.querySelectorAll('.tree-card').forEach(card => {
                const nameElement = card.querySelector('.person-name');
                const photoElement = card.querySelector('.person-photo');
                if (nameElement && photoElement) {
                    const personName = getCleanPersonName(photoElement);
                    const filename = photoMappings[personName];
                    
                    if (filename) {
                        // Try to load image from server images folder
                        const img = new Image();
                        img.onload = function() {
                            photoElement.style.backgroundImage = `url("./images/${filename}")`;
                            photoElement.classList.add('has-image');
                            photoElement.textContent = '';
                        };
                        img.onerror = function() {
                            console.warn(`Image not found: ./images/${filename}`);
                            setInitialsForElement(nameElement, photoElement);
                        };
                        img.src = `./images/${filename}`;
                    } else {
                        setInitialsForElement(nameElement, photoElement);
                    }
                }
            });
        }

        function setInitialsForElement(nameElement, photoElement) {
            const name = nameElement.textContent.split('[')[0].trim().replace(/\(|\)/g, '');
            const initials = name.split(' ').map(n => n[0]).join('');
            photoElement.textContent = initials;
        }

        function setInitials() {
            document.querySelectorAll('.tree-card').forEach(card => {
                const nameElement = card.querySelector('.person-name');
                const photoElement = card.querySelector('.person-photo');
                if (nameElement && photoElement && !photoElement.classList.contains('has-image')) {
                    setInitialsForElement(nameElement, photoElement);
                }
            });
        }

        function toggleDeceasedDate(status) {
            const deceasedDateGroup = document.getElementById('deceasedDateGroup');
            const birthDateGroup = document.getElementById('birthDateGroup');
            if (status === 'deceased') {
                deceasedDateGroup.style.display = 'block';
                birthDateGroup.style.display = 'block';
            } else {
                deceasedDateGroup.style.display = 'none';
                birthDateGroup.style.display = 'block';
            }
        }
    </script>

</body>
</html>
