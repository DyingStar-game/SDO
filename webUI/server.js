const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Configuration EJS
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Servir les fichiers statiques
app.use(express.static(path.join(__dirname, 'public')));

// Route principale
app.get('/', async (req, res) => {
    try {
        // Récupérer les données des serveurs
        const response = await axios.get('https://api.example.com/servers');
        const servers = response.data;
        
        res.render('index', { servers });
    } catch (error) {
        console.error('Erreur lors de la récupération des serveurs:', error.message);
        res.render('index', { servers: [], error: 'Impossible de récupérer les données des serveurs' });
    }
});

// Route API pour récupérer les serveurs (optionnel)
app.get('/api/servers', async (req, res) => {
    try {
        const response = await axios.get('https://api.example.com/servers');
        res.json(response.data);
    } catch (error) {
        console.error('Erreur API:', error.message);
        res.status(500).json({ error: 'Impossible de récupérer les données des serveurs' });
    }
});

app.listen(PORT, () => {
    console.log(`Serveur démarré sur http://localhost:${PORT}`);
});
