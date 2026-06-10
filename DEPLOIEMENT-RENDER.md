# Déploiement sur Render.com

## Étapes

### 1. Pousser sur GitHub
```bash
cd /chemin/vers/406D
git init
git add .
git commit -m "TP WR406D - initial"
# Crée un repo sur github.com puis :
git remote add origin https://github.com/TON_PSEUDO/evalwr406d.git
git push -u origin main
```

### 2. Déployer via Blueprint (automatique)
1. Va sur [render.com](https://render.com) → **New** → **Blueprint**
2. Connecte ton repo GitHub
3. Render lit `render.yaml` et crée les 3 ressources :
   - `evalwr406d-api` (Web Service Docker)
   - `evalwr406d-frontend` (Static Site)
   - `evalwr406d-db` (PostgreSQL gratuit)

### 3. Corriger les URLs croisées
Après le premier déploiement, Render affiche les URLs exactes.

**Backend** (`evalwr406d-api`) → Environment → modifier :
```
CORS_ALLOW_ORIGIN = ^https://evalwr406d-frontend\.onrender\.com$
```

**Frontend** (`evalwr406d-frontend`) → Environment → modifier :
```
VITE_API_URL = https://evalwr406d-api.onrender.com
```
Puis cliquer **Manual Deploy** → **Clear build cache & deploy** sur le frontend.

### 4. Charger les données de démo
Dans Render → evalwr406d-api → **Shell** :
```bash
php bin/console doctrine:fixtures:load --no-interaction
```

## URLs finales
- Frontend : https://evalwr406d-frontend.onrender.com
- API :       https://evalwr406d-api.onrender.com/api
- Swagger :   https://evalwr406d-api.onrender.com/api/docs

## Notes
- Le free tier s'endort après 15 min d'inactivité (redémarrage ~30s)
- La BDD PostgreSQL gratuite expire après 90 jours
- Pour l'URL du TP : configurer un domaine custom dans Render si besoin
