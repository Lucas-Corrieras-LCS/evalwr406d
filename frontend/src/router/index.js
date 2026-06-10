import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import VehiculesView from '../views/VehiculesView.vue'
import VehiculeFormView from '../views/VehiculeFormView.vue'
import MarquesView from '../views/MarquesView.vue'
import MarqueFormView from '../views/MarqueFormView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: HomeView },
    { path: '/vehicules', name: 'vehicules', component: VehiculesView },
    { path: '/vehicules/nouveau', name: 'vehicule-create', component: VehiculeFormView },
    { path: '/vehicules/:id/modifier', name: 'vehicule-edit', component: VehiculeFormView, props: true },
    { path: '/marques', name: 'marques', component: MarquesView },
    { path: '/marques/nouvelle', name: 'marque-create', component: MarqueFormView },
    { path: '/marques/:id/modifier', name: 'marque-edit', component: MarqueFormView, props: true },
  ]
})

export default router
