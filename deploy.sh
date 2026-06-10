#!/bin/bash
# ============================================================
# Script de déploiement VPS - TP WR406D
# URL cible: http://monVPS/travaux/evalwr406d
# ============================================================

set -e

VPS_USER="your_user"        # << MODIFIER
VPS_HOST="your_vps_ip"      # << MODIFIER
REMOTE_DIR="/var/www/html"
APP_PATH="travaux/evalwr406d"

echo "=== Build du frontend Vue.js ==="
cd frontend
npm install
VITE_API_URL="http://$VPS_HOST" npm run build
cd ..

echo "=== Envoi des fichiers frontend sur le VPS ==="
ssh $VPS_USER@$VPS_HOST "mkdir -p $REMOTE_DIR/$APP_PATH"
rsync -avz --delete frontend/dist/ $VPS_USER@$VPS_HOST:$REMOTE_DIR/$APP_PATH/

echo "=== Envoi du backend Symfony ==="
rsync -avz --exclude='.git' --exclude='vendor' --exclude='var' \
  backend/ $VPS_USER@$VPS_HOST:/var/www/evalwr406d-backend/

echo "=== Configuration sur le VPS ==="
ssh $VPS_USER@$VPS_HOST << 'REMOTE'
  cd /var/www/evalwr406d-backend
  # Installer les dépendances PHP
  composer install --no-dev --optimize-autoloader
  # Migrations et fixtures
  php bin/console doctrine:migrations:migrate --no-interaction
  php bin/console doctrine:fixtures:load --no-interaction --append
  echo "Backend configuré"
REMOTE

echo ""
echo "✅ Déploiement terminé !"
echo "👉 Application accessible sur: http://$VPS_HOST/$APP_PATH"
