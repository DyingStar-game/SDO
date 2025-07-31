// Actualisation automatique des données
function refreshData() {
    fetch('/api/servers')
        .then(response => response.json())
        .then(data => {
            console.log('Données actualisées:', data.length, 'serveurs');
            // Vous pouvez implémenter une mise à jour dynamique ici
            // Pour l'instant, on recharge simplement la page
            location.reload();
        })
        .catch(error => {
            console.error('Erreur lors de l\'actualisation:', error);
        });
}

// Actualiser toutes les 30 secondes
setInterval(refreshData, 30000);

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
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
});

// Filtre par statut
function filterByStatus(status) {
    const cards = document.querySelectorAll('.server-card');
    
    cards.forEach(card => {
        if (status === 'all') {
            card.style.display = 'block';
        } else if (status === 'free' && card.classList.contains('free')) {
            card.style.display = 'block';
        } else if (status === 'occupied' && card.classList.contains('occupied')) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Ajouter des boutons de filtre (optionnel - vous pouvez les ajouter au HTML)
function addFilterButtons() {
    const container = document.querySelector('.container');
    const filterDiv = document.createElement('div');
    filterDiv.className = 'filter-buttons';
    filterDiv.innerHTML = `
        <button onclick="filterByStatus('all')" class="filter-btn active">Tous</button>
        <button onclick="filterByStatus('free')" class="filter-btn">Libres</button>
        <button onclick="filterByStatus('occupied')" class="filter-btn">Occupés</button>
    `;
    
    const serversGrid = document.querySelector('.servers-grid');
    container.insertBefore(filterDiv, serversGrid);
}

// Fonction pour copier l'adresse IP:Port
function copyAddress(ip, port) {
    const address = `${ip}:${port}`;
    navigator.clipboard.writeText(address).then(() => {
        // Afficher un message de confirmation
        const toast = document.createElement('div');
        toast.textContent = `Adresse copiée: ${address}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00d4aa;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}

// Ajouter les styles CSS pour l'animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .filter-buttons {
        display: flex;
        gap: 10px;
        margin: 20px 0;
        justify-content: center;
    }
    
    .filter-btn {
        padding: 8px 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.05);
        color: white;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover,
    .filter-btn.active {
        background: #00d4aa;
        border-color: #00d4aa;
    }
`;
document.head.appendChild(style);
