// Système de logs colorés pour le navigateur
const log = {
    info: (msg, ...args) => console.log(`%cℹ️ ${msg}`, 'color: #17a2b8; font-weight: bold;', ...args),
    success: (msg, ...args) => console.log(`%c✅ ${msg}`, 'color: #28a745; font-weight: bold;', ...args),
    warning: (msg, ...args) => console.log(`%c⚠️ ${msg}`, 'color: #ffc107; font-weight: bold;', ...args),
    error: (msg, ...args) => console.log(`%c❌ ${msg}`, 'color: #dc3545; font-weight: bold;', ...args),
    api: (msg, ...args) => console.log(`%c📡 ${msg}`, 'color: #007bff; font-weight: bold;', ...args),
    config: (msg, ...args) => console.log(`%c⚙️ ${msg}`, 'color: #6f42c1; font-weight: bold;', ...args),
    data: (msg, ...args) => console.log(`%c📊 ${msg}`, 'color: #fd7e14; font-weight: bold;', ...args)
};

// Variables globales pour la configuration
let refreshRate = 30; // secondes
let baseUrl = ''; // URL de base vide par défaut, à configurer par l'utilisateur
let refreshInterval = null;
let countdownInterval = null;
let remainingTime = refreshRate;
let currentViewMode = 'servers'; // 'servers' ou 'players'
let currentLayoutMode = 'grid'; // 'grid' ou 'list'

// Fonction pour détecter le type d'appareil
function detectDevice() {
    const width = window.innerWidth;
    if (width <= 768) {
        return 'mobile';
    } else if (width <= 1024) {
        return 'tablet';
    } else {
        return 'desktop';
    }
}

// Fonction pour définir le layout par défaut selon l'appareil
function setDefaultLayout() {
    const device = detectDevice();
    if (device === 'mobile') {
        currentLayoutMode = 'list';
        log.config(`📱 Appareil mobile détecté - Layout: liste`);
    } else {
        currentLayoutMode = 'grid';
        log.config(`🖥️ Appareil ${device} détecté - Layout: grille`);
    }
    applyLayoutMode();
}

// Fonction pour appliquer le mode d'affichage
function applyLayoutMode() {
    const body = document.body;
    const layoutToggle = document.getElementById('layoutToggle');
    
    if (currentLayoutMode === 'list') {
        body.classList.add('layout-list');
        body.classList.remove('layout-grid');
        if (layoutToggle) layoutToggle.checked = true;
    } else {
        body.classList.add('layout-grid');
        body.classList.remove('layout-list');
        if (layoutToggle) layoutToggle.checked = false;
    }
    
    log.info(`🔄 Layout changé vers: ${currentLayoutMode === 'grid' ? 'Grille' : 'Liste'}`);
}

// Fonction pour construire les URLs dynamiquement
function buildUrl(endpoint) {
    if (!baseUrl || baseUrl.trim() === '') {
        return '';
    }
    
    // Nettoyer l'URL de base (enlever le slash final s'il existe)
    let cleanBaseUrl = baseUrl.trim();
    if (cleanBaseUrl.endsWith('/')) {
        cleanBaseUrl = cleanBaseUrl.slice(0, -1);
    }
    
    // Construire l'URL complète
    return `${cleanBaseUrl}/sdo/${endpoint}`;
}

// Fonction pour obtenir l'URL des serveurs
function getServersUrl() {
    return buildUrl('servers');
}

// Fonction pour obtenir l'URL des joueurs
function getPlayersUrl() {
    return buildUrl('players');
}

// Fonction principale d'actualisation des données
function refreshData() {
    if (currentViewMode === 'servers') {
        refreshServers();
    } else {
        refreshPlayers();
    }
}

// Actualisation des serveurs
function refreshServers() {
    const serversUrl = getServersUrl();
    
    // Vérifier si l'URL est configurée
    if (!serversUrl || serversUrl.trim() === '') {
        log.warning('URL de base non configurée, actualisation des serveurs ignorée');
        showError('Veuillez configurer l\'URL de base avant l\'actualisation');
        return;
    }
    
    log.api(`Actualisation des serveurs depuis: ${serversUrl}`);
    
    fetch(serversUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            log.success(`Données serveurs actualisées: ${data.length} serveurs`);
            
            // Mise à jour dynamique des stats
            updateServerStats(data);
            
            // Mise à jour des cartes serveurs
            updateServerCards(data);
            
            // Réinitialiser le compte à rebours
            remainingTime = refreshRate;
            
        })
        .catch(error => {
            log.error(`Erreur lors de l'actualisation des serveurs: ${error.message}`);
            showError(`Erreur serveurs: ${error.message}`);
        });
}

// Actualisation des joueurs
function refreshPlayers() {
    const playersUrl = getPlayersUrl();
    
    // Vérifier si l'URL est configurée
    if (!playersUrl || playersUrl.trim() === '') {
        log.warning('URL de base non configurée, actualisation des joueurs ignorée');
        showError('Veuillez configurer l\'URL de base avant l\'actualisation');
        return;
    }
    
    log.api(`Actualisation des joueurs depuis: ${playersUrl}`);
    
    fetch(playersUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            log.success(`Données joueurs actualisées: ${data.length} joueurs`);
            
            // Mise à jour dynamique des stats
            updatePlayerStats(data);
            
            // Mise à jour des cartes joueurs
            updatePlayerCards(data);
            
            // Réinitialiser le compte à rebours
            remainingTime = refreshRate;
            
        })
        .catch(error => {
            log.error(`Erreur lors de l'actualisation des joueurs: ${error.message}`);
            showError(`Erreur joueurs: ${error.message}`);
        });
}

// Mise à jour des statistiques des serveurs
function updateServerStats(servers) {
    const totalServers = servers.length;
    const freeServers = servers.filter(s => s.is_free === 1).length;
    const occupiedServers = servers.filter(s => s.is_free === 0).length;
    
    log.data(`📈 Stats serveurs: ${totalServers} total | ${freeServers} libres | ${occupiedServers} occupés`);
    
    // Mettre à jour les éléments de stats serveurs
    const statCards = document.querySelectorAll('.servers-stats .stat-card h3');
    if (statCards.length >= 3) {
        statCards[0].textContent = totalServers;
        statCards[1].textContent = freeServers;
        statCards[2].textContent = occupiedServers;
    }
}

// Mise à jour des statistiques des joueurs
function updatePlayerStats(players) {
    const totalPlayers = players.length;
    const uniqueServers = [...new Set(players.map(p => p.server_id))].length;
    const recentlyUpdated = players.filter(p => {
        const updatedAt = new Date(p.updated_at);
        const now = new Date();
        const timeDiff = now - updatedAt;
        return timeDiff < 5 * 60 * 1000; // Actif dans les 5 dernières minutes
    }).length;
    
    log.data(`📈 Stats joueurs: ${totalPlayers} total | ${uniqueServers} serveurs uniques | ${recentlyUpdated} récemment actifs`);
    
    // Mettre à jour les éléments de stats joueurs
    document.getElementById('totalPlayers').textContent = totalPlayers;
    document.getElementById('uniqueServers').textContent = uniqueServers;
    document.getElementById('recentlyUpdated').textContent = recentlyUpdated;
}

// Mise à jour des cartes serveurs
function updateServerCards(servers) {
    const serversGrid = document.querySelector('.servers-grid');
    if (!serversGrid) return;
    
    serversGrid.innerHTML = '';
    
    servers.forEach(server => {
        const serverCard = createServerCard(server);
        serversGrid.appendChild(serverCard);
    });
    
    if (servers.length === 0) {
        serversGrid.innerHTML = '<div class="no-servers"><p>🔍 Aucun serveur trouvé</p></div>';
    }
}

// Mise à jour des cartes joueurs
function updatePlayerCards(players) {
    const playersGrid = document.querySelector('.players-grid');
    if (!playersGrid) return;
    
    playersGrid.innerHTML = '';
    
    players.forEach(player => {
        const playerCard = createPlayerCard(player);
        playersGrid.appendChild(playerCard);
    });
    
    if (players.length === 0) {
        playersGrid.innerHTML = '<div class="no-servers"><p>🔍 Aucun joueur trouvé</p></div>';
    }
}

// Créer une carte serveur
function createServerCard(server) {
    const card = document.createElement('div');
    card.className = `server-card ${server.is_free === 1 ? 'free' : 'occupied'}`;
    
    card.innerHTML = `
        <div class="server-header">
            <h3>${server.name}</h3>
            <span class="status-badge ${server.is_free === 1 ? 'free' : 'occupied'}">
                ${server.is_free === 1 ? '✅ Libre' : '🔴 Occupé'}
            </span>
        </div>
        
        <div class="server-info">
            <div class="info-row">
                <span class="label">🌐 Adresse:</span>
                <span class="value">${server.ip}:${server.port}</span>
            </div>
            
            <div class="info-row">
                <span class="label">👥 Joueurs:</span>
                <span class="value">${server.current_players}/${server.max_players}</span>
            </div>
            
            ${server.coordinate_x_start !== null ? `
                <div class="info-row">
                    <span class="label">📍 Coordonnées X:</span>
                    <span class="value">${server.coordinate_x_start} → ${server.coordinate_x_end}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">📍 Coordonnées Y:</span>
                    <span class="value">${server.coordinate_y_start} → ${server.coordinate_y_end}</span>
                </div>
                
                <div class="info-row">
                    <span class="label">📍 Coordonnées Z:</span>
                    <span class="value">${server.coordinate_z_start} → ${server.coordinate_z_end}</span>
                </div>
            ` : `
                <div class="info-row">
                    <span class="label">📍 Coordonnées:</span>
                    <span class="value">Non définies</span>
                </div>
            `}
            
            <div class="info-row">
                <span class="label">🕐 Créé le:</span>
                <span class="value">${new Date(server.created_at).toLocaleString('fr-FR')}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🔄 Mis à jour:</span>
                <span class="value">${new Date(server.updated_at).toLocaleString('fr-FR')}</span>
            </div>
        </div>
    `;
    
    return card;
}

// Créer une carte joueur
function createPlayerCard(player) {
    const card = document.createElement('div');
    card.className = 'player-card';
    
    card.innerHTML = `
        <div class="player-header">
            <h3>${player.name}</h3>
            <span class="player-id">ID: ${player.id}</span>
        </div>
        
        <div class="player-info">
            <div class="info-row">
                <span class="label">🆔 Client ID:</span>
                <span class="value">${player.client_id}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🖥️ Serveur:</span>
                <span class="value server-badge">Serveur ${player.server_id}</span>
            </div>
            
            <div class="info-row">
                <span class="label">📍 Position:</span>
                <span class="value coordinates-badge">X: ${player.x}, Y: ${player.y}, Z: ${player.z}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🕐 Créé le:</span>
                <span class="value">${new Date(player.created_at).toLocaleString('fr-FR')}</span>
            </div>
            
            <div class="info-row">
                <span class="label">🔄 Mis à jour:</span>
                <span class="value">${new Date(player.updated_at).toLocaleString('fr-FR')}</span>
            </div>
        </div>
    `;
    
    return card;
}

// Basculer entre les vues
function toggleView() {
    const body = document.body;
    const toggle = document.getElementById('viewToggle');
    
    if (toggle.checked) {
        currentViewMode = 'players';
        body.className = 'view-players';
        log.info('🔄 Basculement vers la vue Joueurs');
        refreshPlayers();
    } else {
        currentViewMode = 'servers';
        body.className = 'view-servers';
        log.info('🔄 Basculement vers la vue Serveurs');
        refreshServers();
    }
    
    // Réappliquer le layout mode
    applyLayoutMode();
    
    // Réinitialiser le compteur
    remainingTime = refreshRate;
}

// Basculer entre grille et liste
function toggleLayout() {
    const toggle = document.getElementById('layoutToggle');
    
    if (toggle.checked) {
        currentLayoutMode = 'list';
    } else {
        currentLayoutMode = 'grid';
    }
    
    applyLayoutMode();
}

// Fonction pour démarrer les intervalles
function startRefreshInterval() {
    // Arrêter les intervalles existants
    if (refreshInterval) clearInterval(refreshInterval);
    if (countdownInterval) clearInterval(countdownInterval);
    
    // Démarrer les nouveaux intervalles
    refreshInterval = setInterval(refreshData, refreshRate * 1000);
    
    // Compteur visuel
    countdownInterval = setInterval(() => {
        remainingTime--;
        updateCountdown();
        
        if (remainingTime <= 0) {
            remainingTime = refreshRate;
        }
    }, 1000);
}

// Mise à jour du compteur visuel
function updateCountdown() {
    const countdownTime = document.getElementById('countdownTime');
    const countdownBar = document.getElementById('countdownBar');
    
    if (countdownTime) {
        countdownTime.textContent = remainingTime;
    }
    
    if (countdownBar) {
        const percentage = ((refreshRate - remainingTime) / refreshRate) * 100;
        countdownBar.style.width = percentage + '%';
    }
}

// Fonction pour afficher les erreurs
function showError(message) {
    // Créer un élément toast pour l'erreur
    const toast = document.createElement('div');
    toast.className = 'error-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">⚠️</span>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Ajouter les styles si pas déjà fait
    if (!document.querySelector('#error-toast-styles')) {
        const style = document.createElement('style');
        style.id = 'error-toast-styles';
        style.textContent = `
            .error-toast {
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(220, 53, 69, 0.95);
                color: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 1000;
                animation: slideInToast 0.3s ease;
                max-width: 400px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(220, 53, 69, 0.3);
            }
            
            .toast-content {
                display: flex;
                align-items: center;
                gap: 0.8rem;
            }
            
            .toast-icon {
                font-size: 1.2rem;
            }
            
            .toast-message {
                flex: 1;
                font-size: 0.9rem;
            }
            
            .toast-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.2rem;
                cursor: pointer;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            @keyframes slideInToast {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Fonction pour afficher les messages de succès
function showSuccess(message) {
    const toast = document.createElement('div');
    toast.className = 'success-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">✅</span>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Ajouter les styles pour le toast de succès
    if (!document.querySelector('#success-toast-styles')) {
        const style = document.createElement('style');
        style.id = 'success-toast-styles';
        style.textContent = `
            .success-toast {
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(40, 167, 69, 0.95);
                color: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 1000;
                animation: slideInToast 0.3s ease;
                max-width: 400px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(40, 167, 69, 0.3);
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    // Supprimer automatiquement après 3 secondes
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    log.success('🚀 WebUI Star Deception initialisée');
    log.config(`Mode initial: ${currentViewMode} | Refresh: ${refreshRate}s`);
    
    // Détecter et définir le layout par défaut
    setDefaultLayout();
    
    // Configurer les valeurs initiales
    document.getElementById('refreshRate').value = refreshRate;
    document.getElementById('baseUrl').value = baseUrl;
    
    // Définir la vue initiale
    document.body.className = 'view-servers';
    applyLayoutMode();
    
    // Gérer le toggle des vues
    const viewToggle = document.getElementById('viewToggle');
    if (viewToggle) {
        viewToggle.addEventListener('change', toggleView);
    }
    
    // Gérer le toggle du layout
    const layoutToggle = document.getElementById('layoutToggle');
    if (layoutToggle) {
        layoutToggle.addEventListener('change', toggleLayout);
    }
    
    // Gérer le bouton d'application des paramètres
    const applyButton = document.getElementById('applySettings');
    if (applyButton) {
        applyButton.addEventListener('click', function() {
            refreshRate = parseInt(document.getElementById('refreshRate').value);
            baseUrl = document.getElementById('baseUrl').value.trim();
            
            log.config('Nouveaux paramètres appliqués:', {
                refreshRate,
                baseUrl,
                serversUrl: getServersUrl(),
                playersUrl: getPlayersUrl(),
                currentViewMode
            });
            
            remainingTime = refreshRate;
            startRefreshInterval();
            refreshData();
            
            showSuccess('Paramètres appliqués avec succès !');
        });
    }
    
    // Ne démarrer l'actualisation automatique que si l'URL de base est configurée
    if (baseUrl.trim() !== '') {
        startRefreshInterval();
        // Première actualisation
        refreshData();
    }
});

// Redétecter le layout lors du redimensionnement
window.addEventListener('resize', function() {
    const device = detectDevice();
    const newDefaultLayout = (device === 'mobile') ? 'list' : 'grid';
    
    // Si l'utilisateur n'a pas changé manuellement le layout, l'adapter automatiquement
    if (currentLayoutMode !== newDefaultLayout) {
        log.config(`🔄 Redimensionnement détecté - Nouveau layout suggéré: ${newDefaultLayout}`);
        // Note: On ne change pas automatiquement pour respecter le choix de l'utilisateur
        // currentLayoutMode = newDefaultLayout;
        // applyLayoutMode();
    }
});

// Ajouter les styles CSS pour les animations
const style = document.createElement('style');
style.textContent = `
    .no-servers {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: #666;
        font-size: 1.2rem;
    }
`;
document.head.appendChild(style);
