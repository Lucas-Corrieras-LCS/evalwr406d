# Frontend Vue.js - TP WR406D

## Développement

```bash
cd frontend
npm install
npm run dev
```

App disponible sur: http://localhost:5173/travaux/evalwr406d/

## Build pour production

```bash
npm run build
```

Les fichiers sont générés dans `dist/`, à placer dans `/var/www/html/travaux/evalwr406d/` sur le VPS.

## Configuration

- `.env` → développement local (API sur localhost:8080)
- `.env.production` → production (modifier VITE_API_URL avec votre VPS)
