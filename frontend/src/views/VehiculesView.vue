<template>
  <div class="page">
    <div class="page-header">
      <h1><Icons name="car" :size="28" color="#e94560" /> Flotte</h1>
      <RouterLink class="btn btn-primary" to="/vehicules/nouveau">
        <Icons name="plus" :size="16" /> Ajouter
      </RouterLink>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
      <div class="search-box">
        <Icons name="search" :size="16" color="#5a5a7a" />
        <input v-model="search" placeholder="Rechercher un modèle..." />
        <button v-if="search" @click="search = ''" style="background:none;border:none;cursor:pointer;padding:0;display:flex;">
          <Icons name="x" :size="14" color="#5a5a7a" />
        </button>
      </div>
      <div class="filter-chips">
        <button class="chip" :class="{ active: activeMarque === null }" @click="activeMarque = null">Tous</button>
        <button
          v-for="m in marquesUniques"
          :key="m.id"
          class="chip"
          :class="{ active: activeMarque === m.id }"
          @click="activeMarque = m.id"
        >{{ m.nom }}</button>
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="skeleton-grid">
      <div class="skeleton-card" v-for="n in 6" :key="n">
        <div class="skeleton-img"><div class="skeleton-line"></div></div>
        <div class="skeleton-body">
          <div class="skeleton-line w-60"></div>
          <div class="skeleton-line w-40"></div>
          <div class="skeleton-line w-80"></div>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else-if="filtered.length === 0" class="empty-state">
      <Icons name="car" :size="80" />
      <p>{{ search || activeMarque ? 'Aucun résultat pour ce filtre.' : 'Aucun véhicule trouvé.' }}</p>
      <RouterLink v-if="!search && !activeMarque" class="btn btn-primary" to="/vehicules/nouveau">
        <Icons name="plus" :size="16" /> Premier véhicule
      </RouterLink>
    </div>

    <!-- Grid -->
    <div v-else class="cards-grid">
      <div
        v-for="(v, i) in filtered"
        :key="v.id"
        class="card"
        ref="cardRefs"
        :style="`transition-delay:${Math.min(i,8)*0.07}s`"
      >
        <div class="card-img">
          <img v-if="v.photo" :src="v.photo" :alt="v.modele" loading="lazy" />
          <div v-else class="card-img-placeholder">
            <Icons name="car-placeholder" :size="160" />
          </div>
          <div class="card-gradient-line"></div>
        </div>
        <div class="card-body">
          <div class="card-title">{{ v.modele }}</div>
          <div class="card-brand">{{ v.marque?.nom }} · {{ v.marque?.pays }}</div>
          <div class="card-stats">
            <div class="stat-pill">
              <div class="stat-pill-label"><Icons name="euro" :size="11" /> Prix</div>
              <div class="stat-pill-value">{{ formatPrix(v.prix) }}</div>
            </div>
            <div class="stat-pill">
              <div class="stat-pill-label"><Icons name="gauge" :size="11" /> Puissance</div>
              <div class="stat-pill-value">{{ v.puissance }} ch</div>
            </div>
            <div class="stat-pill">
              <div class="stat-pill-label"><Icons name="calendar" :size="11" /> Année</div>
              <div class="stat-pill-value">{{ v.annee }}</div>
            </div>
            <div class="stat-pill">
              <div class="stat-pill-label"><Icons name="globe" :size="11" /> Pays</div>
              <div class="stat-pill-value">{{ v.marque?.pays }}</div>
            </div>
          </div>
          <div class="card-actions">
            <RouterLink :to="`/vehicules/${v.id}/modifier`" class="btn btn-secondary btn-sm">
              <Icons name="edit" :size="13" /> Modifier
            </RouterLink>
            <button class="btn btn-danger btn-sm" @click="confirmDelete(v)">
              <Icons name="trash" :size="13" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Transition name="page">
      <div v-if="toDelete" class="modal-overlay" @click.self="toDelete = null">
        <div class="modal">
          <h3>Supprimer ce véhicule ?</h3>
          <p>{{ toDelete.modele }} sera définitivement supprimé.</p>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="toDelete = null">Annuler</button>
            <button class="btn btn-danger" @click="deleteVehicule">
              <Icons name="trash" :size="14" /> Confirmer
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <Transition name="page">
      <div v-if="toast" :class="`toast toast-${toastType}`">{{ toast }}</div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { RouterLink } from 'vue-router'
import Icons from '../components/Icons.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'

const vehicules = ref([])
const loading   = ref(true)
const toDelete  = ref(null)
const search    = ref('')
const activeMarque = ref(null)
const toast     = ref('')
const toastType = ref('success')
const cardRefs  = ref([])

// Gère à la fois un objet embarqué { id, nom, ... }
// et une IRI string "/api/marques/1" (fallback API Platform)
function marqueId(marque) {
  if (!marque) return null
  if (typeof marque === 'object') return marque.id
  const m = String(marque).match(/\/(\d+)$/)
  return m ? parseInt(m[1]) : null
}
function marqueName(marque) {
  if (!marque) return ''
  if (typeof marque === 'object') return marque.nom ?? ''
  return ''
}

const marquesUniques = computed(() => {
  const seen = new Map()
  vehicules.value.forEach(v => {
    if (!v.marque) return
    const id = marqueId(v.marque)
    if (id && !seen.has(id)) seen.set(id, { id, nom: marqueName(v.marque) || `Marque #${id}`, pays: v.marque?.pays ?? '' })
  })
  return [...seen.values()].sort((a, b) => a.nom.localeCompare(b.nom))
})

const filtered = computed(() => vehicules.value.filter(v => {
  const q = search.value.toLowerCase()
  const nom = (typeof v.marque === 'object' ? v.marque?.nom : '') ?? ''
  const matchSearch = !q || v.modele.toLowerCase().includes(q) || nom.toLowerCase().includes(q)
  const matchMarque = !activeMarque.value || marqueId(v.marque) === activeMarque.value
  return matchSearch && matchMarque
}))

/* scroll reveal */
let observer
function setupObserver() {
  observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target) } })
  }, { threshold: 0.08 })
  nextTick(() => { cardRefs.value.forEach(el => el && observer.observe(el)) })
}

async function loadVehicules() {
  loading.value = true
  try {
    const res = await fetch(`${API_URL}/api/vehicules`)
    const data = await res.json()
    vehicules.value = data['hydra:member'] ?? []
    setupObserver()
  } catch { showToast('Erreur de chargement', 'error') }
  finally { loading.value = false }
}

function formatPrix(p) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 }).format(p)
}

function confirmDelete(v) { toDelete.value = v }

async function deleteVehicule() {
  try {
    const res = await fetch(`${API_URL}/api/vehicules/${toDelete.value.id}`, { method: 'DELETE' })
    if (res.ok) {
      vehicules.value = vehicules.value.filter(v => v.id !== toDelete.value.id)
      showToast('Véhicule supprimé', 'success')
    } else showToast('Erreur suppression', 'error')
  } catch { showToast('Erreur réseau', 'error') }
  finally { toDelete.value = null }
}

function showToast(msg, type = 'success') {
  toast.value = msg; toastType.value = type
  setTimeout(() => toast.value = '', 3000)
}

onMounted(loadVehicules)
</script>
