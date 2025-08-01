const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();

// Codes couleur ANSI pour les logs
const colors = {
    reset: '\x1b[0m',
    bright: '\x1b[1m',
    red: '\x1b[31m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    magenta: '\x1b[35m',
    cyan: '\x1b[36m',
    white: '\x1b[37m',
    bgRed: '\x1b[41m',
    bgGreen: '\x1b[42m',
    bgYellow: '\x1b[43m',
    bgBlue: '\x1b[44m',
    bgCyan: '\x1b[46m'
};

// Fonction pour logger avec style
const log = {
    info: (msg) => console.log(`${colors.cyan}ℹ️  ${msg}${colors.reset}`),
    success: (msg) => console.log(`${colors.green}✅ ${msg}${colors.reset}`),
    warning: (msg) => console.log(`${colors.yellow}⚠️  ${msg}${colors.reset}`),
    error: (msg) => console.log(`${colors.red}❌ ${msg}${colors.reset}`),
    server: (msg) => console.log(`${colors.magenta}🚀 ${msg}${colors.reset}`),
    api: (msg) => console.log(`${colors.blue}📡 ${msg}${colors.reset}`),
    config: (msg) => console.log(`${colors.cyan}⚙️  ${msg}${colors.reset}`)
};

// Configuration du port avec plusieurs options :
// 1. Argument --port=XXXX en ligne de commande
// 2. Variable d'environnement PORT
// 3. Valeur par défaut 3000
const getPortFromArgs = () => {
    const portArg = process.argv.find(arg => arg.startsWith('--port='));
    return portArg ? parseInt(portArg.split('=')[1]) : null;
};

const PORT = getPortFromArgs() || process.env.PORT || 3000;

// Configuration EJS
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Servir les fichiers statiques
app.use(express.static(path.join(__dirname, 'public')));

// Middleware de logging des requêtes
app.use((req, res, next) => {
    const timestamp = new Date().toLocaleTimeString('fr-FR');
    log.info(`${colors.bright}${req.method}${colors.reset} ${req.path} - ${colors.yellow}${timestamp}${colors.reset}`);
    next();
});

// Route principale
app.get('/', async (req, res) => {
    log.info('Accès à la page principale');
    // Renvoyer la page avec des données vides par défaut
    // L'utilisateur devra configurer les URLs dans l'interface
    res.render('index', { 
        servers: [], 
        players: [], 
        viewMode: 'servers',
        message: 'Veuillez configurer les URLs dans les paramètres pour commencer l\'actualisation' 
    });
});

// Route API pour récupérer les serveurs
app.get('/api/servers', async (req, res) => {
    const url = req.query.url;
    
    if (!url) {
        log.warning('Tentative d\'accès API serveurs sans URL');
        return res.status(400).json({ error: 'URL manquante. Utilisez ?url=... pour spécifier l\'URL des serveurs' });
    }
    
    try {
        log.api(`Récupération des serveurs depuis: ${colors.bright}${url}${colors.reset}`);
        const response = await axios.get(url);
        log.success(`${response.data.length || 0} serveurs récupérés avec succès`);
        res.json(response.data);
    } catch (error) {
        log.error(`Échec récupération serveurs: ${error.message}`);
        res.status(500).json({ error: `Impossible de récupérer les données depuis ${url}: ${error.message}` });
    }
});

// Route API pour récupérer les joueurs
app.get('/api/players', async (req, res) => {
    try {
        log.api('Récupération des données joueurs...');
        const response = await axios.get('http://sdo.stardeception.space/sdo/players');
        log.success(`${response.data.length || 0} joueurs récupérés avec succès`);
        res.json(response.data);
    } catch (error) {
        log.error(`Échec récupération joueurs: ${error.message}`);
        res.status(500).json({ error: 'Impossible de récupérer les données des joueurs' });
    }
});

// Gestion des erreurs 404
app.use((req, res) => {
    log.warning(`Page non trouvée: ${req.path}`);
    res.status(404).json({ 
        error: '404 - Page non trouvée',
        message: 'La ressource demandée n\'existe pas'
    });
});

// Gestion des erreurs globales
app.use((error, req, res, next) => {
    log.error(`Erreur serveur: ${error.message}`);
    res.status(500).json({
        error: '500 - Erreur interne du serveur',
        message: 'Une erreur inattendue s\'est produite'
    });
});

app.listen(PORT, () => {
    console.log('\n' + '='.repeat(60));
    log.server(`${colors.bright}Star Deception WebUI - Sentinel${colors.reset}`);
    console.log('='.repeat(60));
    log.success(`Serveur démarré avec succès !`);
    log.api(`URL d'accès: ${colors.bright}${colors.cyan}http://localhost:${PORT}${colors.reset}`);
    log.config(`Port configuré: ${colors.bright}${PORT}${colors.reset}`);
    console.log('\n' + colors.yellow + '💡 Conseils de démarrage:' + colors.reset);
    console.log(`   ${colors.cyan}•${colors.reset} Ligne de commande: ${colors.bright}node server.js --port=XXXX${colors.reset}`);
    console.log(`   ${colors.cyan}•${colors.reset} Variable d'env: ${colors.bright}$env:PORT=XXXX; node server.js${colors.reset}`);
    console.log(`   ${colors.cyan}•${colors.reset} Scripts npm: ${colors.bright}npm run start:8080|5000|4000${colors.reset}`);
    console.log('\n' + colors.green + '🎯 Serveur prêt à recevoir des requêtes !' + colors.reset + '\n');
});
