// Variables globales pour la configuration
let refreshRate = 30; // secondes
let dataUrl = 'https://api.example.com/servers';
let refreshInterval = null;
let countdownInterval = null;
let remainingTime = refreshRate;

// Fonction principale d'actualisation des données
function refreshData() {
    console.log(`Actualisation des données depuis: ${dataUrl}`);
    
    fetch(dataUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données actualisées:', data.length, 'serveurs');
            
            // Mise à jour dynamique des stats
            updateStats(data);
            
            // Mise à jour des cartes serveurs
            updateServerCards(data);
            
            // Réinitialiser le compte à rebours
            remainingTime = refreshRate;
            
        })
        .catch(error => {
            console.error('Erreur lors de l\'actualisation:', error);
            showError(`Erreur: ${error.message}`);
        });
}

// Mise à jour des statistiques
function updateStats(servers) {
    const totalServers = servers.length;
    const freeServers = servers.filter(s => s.is_free === 1).length;
    const occupiedServers = servers.filter(s => s.is_free === 0).length;
    
    // Mettre à jour les éléments de stats s'ils existent
    const statCards = document.querySelectorAll('.stat-card h3');
    if (statCards.length >= 3) {
        statCards[0].textContent = totalServers;
        statCards[1].textContent = freeServers;
        statCards[2].textContent = occupiedServers;
    }
}

// Mise à jour des cartes serveurs
function updateServerCards(servers) {
    const serversGrid = document.querySelector('.servers-grid');
    if (!serversGrid) return;
    
    // Nettoyer la grille actuelle
    serversGrid.innerHTML = '';
    
    // Recréer les cartes
    servers.forEach((server, index) => {
        const serverCard = createServerCard(server);
        serversGrid.appendChild(serverCard);
        
        // Animation d'apparition
        setTimeout(() => {
            serverCard.style.opacity = '1';
            serverCard.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Afficher message si aucun serveur
    if (servers.length === 0) {
        serversGrid.innerHTML = '<div class="no-servers"><p>🔍 Aucun serveur trouvé</p></div>';
    }
}

// Créer une carte serveur
function createServerCard(server) {
    const card = document.createElement('div');
    card.className = `server-card ${server.is_free === 1 ? 'free' : 'occupied'}`;
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.5s ease';
    
    const coordinatesHtml = server.coordinate_x_start !== null ? `
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
    `;
    
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
            
            ${coordinatesHtml}
            
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

// Démarrer le système de refresh
function startRefreshSystem() {
    // Arrêter les intervals existants
    if (refreshInterval) clearInterval(refreshInterval);
    if (countdownInterval) clearInterval(countdownInterval);
    
    // Refresh principal
    refreshInterval = setInterval(refreshData, refreshRate * 1000);
    
    // Compte à rebours
    remainingTime = refreshRate;
    countdownInterval = setInterval(updateCountdown, 1000);
    
    console.log(`Système de refresh démarré: ${refreshRate}s`);
}

// Mettre à jour le compte à rebours
function updateCountdown() {
    remainingTime--;
    
    const countdownTimeElement = document.getElementById('countdownTime');
    const countdownBarElement = document.getElementById('countdownBar');
    const countdownDisplayElement = document.querySelector('.countdown-display');
    
    if (countdownTimeElement) {
        countdownTimeElement.textContent = remainingTime;
    }
    
    // Mettre à jour la barre de progression
    if (countdownBarElement) {
        const progressPercentage = (remainingTime / refreshRate) * 100;
        countdownBarElement.style.width = `${progressPercentage}%`;
    }
    
    // Animation d'alerte quand il reste moins de 5 secondes
    if (countdownDisplayElement) {
        if (remainingTime <= 5 && remainingTime > 0) {
            countdownDisplayElement.classList.add('warning');
        } else {
            countdownDisplayElement.classList.remove('warning');
        }
    }
    
    if (remainingTime <= 0) {
        remainingTime = refreshRate;
        // Reset la barre de progression
        if (countdownBarElement) {
            countdownBarElement.style.width = '100%';
        }
        if (countdownDisplayElement) {
            countdownDisplayElement.classList.remove('warning');
        }
    }
}

// Appliquer les nouveaux paramètres
function applySettings() {
    const newRefreshRate = parseInt(document.getElementById('refreshRate').value);
    const newDataUrl = document.getElementById('dataUrl').value.trim();
    
    // Validation
    if (newRefreshRate < 5 || newRefreshRate > 300) {
        showError('Le refresh rate doit être entre 5 et 300 secondes');
        return;
    }
    
    if (!newDataUrl) {
        showError('L\'URL des données ne peut pas être vide');
        return;
    }
    
    // Appliquer les changements
    refreshRate = newRefreshRate;
    dataUrl = newDataUrl;
    
    // Réinitialiser le compteur
    remainingTime = refreshRate;
    updateCountdownDisplay();
    
    // Redémarrer le système
    startRefreshSystem();
    
    // Refresh immédiat
    refreshData();
    
    // Sauvegarder dans localStorage
    localStorage.setItem('refreshRate', refreshRate);
    localStorage.setItem('dataUrl', dataUrl);
    
    showSuccess(`Paramètres appliqués: Refresh ${refreshRate}s`);
}

// Afficher une erreur
function showError(message) {
    showToast(message, 'error');
}

// Afficher un succès
function showSuccess(message) {
    showToast(message, 'success');
}

// Afficher un toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.className = `toast toast-${type}`;
    
    const colors = {
        success: '#00d4aa',
        error: '#ff6b6b',
        info: '#00a3ff'
    };
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type]};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 1000;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        animation: slideInToast 0.3s ease;
        max-width: 300px;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutToast 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Charger les paramètres sauvegardés
function loadSavedSettings() {
    const savedRefreshRate = localStorage.getItem('refreshRate');
    const savedDataUrl = localStorage.getItem('dataUrl');
    
    if (savedRefreshRate) {
        refreshRate = parseInt(savedRefreshRate);
        document.getElementById('refreshRate').value = refreshRate;
    }
    
    if (savedDataUrl) {
        dataUrl = savedDataUrl;
        document.getElementById('dataUrl').value = dataUrl;
    }
    
    // Initialiser le compteur
    remainingTime = refreshRate;
    updateCountdownDisplay();
}

// Mettre à jour l'affichage du compteur
function updateCountdownDisplay() {
    const countdownTimeElement = document.getElementById('countdownTime');
    const countdownBarElement = document.getElementById('countdownBar');
    
    if (countdownTimeElement) {
        countdownTimeElement.textContent = remainingTime;
    }
    
    if (countdownBarElement) {
        countdownBarElement.style.width = '100%';
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Charger les paramètres
    loadSavedSettings();
    
    // Ajouter les event listeners
    document.getElementById('applySettings').addEventListener('click', applySettings);
    
    // Event listeners pour appliquer avec Entrée
    document.getElementById('refreshRate').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applySettings();
    });
    
    document.getElementById('dataUrl').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applySettings();
    });
    
    // Animation initiale des cartes existantes
    const cards = document.querySelectorAll('.server-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Démarrer le système de refresh
    startRefreshSystem();
    
    console.log('WebUI initialisée avec refresh automatique');
});

// Ajouter les styles CSS pour les animations
const style = document.createElement('style');
style.textContent = `
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
    
    @keyframes slideOutToast {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .no-servers {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: #666;
        font-size: 1.2rem;
    }
`;
document.head.appendChild(style);
