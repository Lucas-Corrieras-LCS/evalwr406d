<template>
  <div class="page">
    <div class="page-header">
      <h1><Icons name="tag" :size="26" color="#e94560" /> Marques</h1>
      <RouterLink class="btn btn-primary" to="/marques/nouvelle">
        <Icons name="plus" :size="16" /> Ajouter
      </RouterLink>
    </div>

    <div v-if="loading" class="skeleton-grid" style="grid-template-columns:1fr">
      <div class="skeleton-card" v-for="n in 4" :key="n" style="height:60px;display:flex;align-items:center;padding:1rem 1.2rem;gap:1rem">
        <div class="skeleton-line" style="width:30%;height:14px;margin:0"></div>
        <div class="skeleton-line" style="width:20%;height:14px;margin:0"></div>
        <div class="skeleton-line" style="width:15%;height:14px;margin:0"></div>
      </div>
    </div>

    <div v-else-if="marques.length === 0" class="empty-state">
      <Icons name="tag" :size="60" />
      <p>Aucune marque enregistrée.</p>
    </div>

    <div v-else class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Marque</th>
            <th><Icons name="globe" :size="13" /> Pays</th>
            <th><Icons name="calendar" :size="13" /> Fondée</th>
            <th><Icons name="car" :size="13" /> Véhicules</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in marques" :key="m.id">
            <td>{{ m.nom }}</td>
            <td><span class="badge">{{ m.pays }}</span></td>
            <td>{{ m.anneeCreation }}</td>
            <td>{{ m.vehicules?.length ?? 0 }}</td>
            <td>
              <div style="display:flex;gap:0.6rem">
                <RouterLink :to="`/marques/${m.id}/modifier`" class="btn btn-secondary btn-sm">
                  <Icons name="edit" :size="13" /> Modifier
                </RouterLink>
                <button class="btn btn-danger btn-sm" @click="confirmDelete(m)">
                  <Icons name="trash" :size="13" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Transition name="page">
      <div v-if="toDelete" class="modal-overlay" @click.self="toDelete = null">
        <div class="modal">
          <h3>Supprimer cette marque ?</h3>
          <p>La marque <strong style="color:var(--text)">{{ toDelete.nom }}</strong> sera supprimée.</p>
          <p class="warn">Les véhicules associés seront aussi supprimés.</p>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="toDelete = null">Annuler</button>
            <button class="btn btn-danger" @click="deleteMarque"><Icons name="trash" :size="13" /> Confirmer</button>
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
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Icons from '../components/Icons.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'
const marques  = ref([])
const loading  = ref(true)
const toDelete = ref(null)
const toast    = ref('')
const toastType = ref('success')

async function load() {
  loading.value = true
  try { marques.value = ((await (await fetch(`${API_URL}/api/marques`)).json())['hydra:member']) ?? [] }
  catch { showToast('Erreur chargement', 'error') }
  finally { loading.value = false }
}

function confirmDelete(m) { toDelete.value = m }
async function deleteMarque() {
  try {
    const res = await fetch(`${API_URL}/api/marques/${toDelete.value.id}`, { method: 'DELETE' })
    if (res.ok) { marques.value = marques.value.filter(m => m.id !== toDelete.value.id); showToast('Marque supprimée') }
    else showToast('Erreur suppression', 'error')
  } catch { showToast('Erreur réseau', 'error') }
  finally { toDelete.value = null }
}

function showToast(msg, type = 'success') { toast.value = msg; toastType.value = type; setTimeout(() => toast.value = '', 3000) }
onMounted(load)
</script>
