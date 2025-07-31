# Star Deception Servers WebApp

Une application web Node.js pour afficher les serveurs Star Deception sous forme de cartes dans une grille.

## Installation

1. Cloner ou télécharger le projet
2. Installer les dépendances :
   ```bash
   npm install
   ```

## Utilisation

### Démarrer le serveur de développement :
```bash
npm run dev
```

### Démarrer le serveur en production :
```bash
npm start
```

L'application sera accessible sur http://localhost:3000

## API

L'application récupère les données depuis :
- **Source** : `http://sdo.stardeception.space/sdo/servers`
- **Endpoint local** : `/api/servers` (proxy)

## Technologies utilisées

- **Backend** : Node.js, Express
- **Template** : EJS
- **HTTP Client** : Axios
- **Frontend** : HTML5, CSS3, JavaScript ES6
- **Design** : CSS Grid, Flexbox, animations CSS

---

## 👨‍💻 Contributeur

**WebUI développée par [𝕭𝖗𝖚𝖒𝖊](https://noasecond.com)**