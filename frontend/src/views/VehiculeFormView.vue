<template>
  <div class="page">
    <div class="page-header">
      <h1>
        <Icons :name="isEdit ? 'edit' : 'plus'" :size="26" color="#e94560" />
        {{ isEdit ? 'Modifier' : 'Nouveau véhicule' }}
      </h1>
      <RouterLink class="btn btn-secondary" to="/vehicules">
        <Icons name="back" :size="16" /> Retour
      </RouterLink>
    </div>

    <div v-if="loading" class="empty-state"><p>Chargement...</p></div>

    <div v-else class="form-wrapper">
      <div class="form-glass">
        <div v-if="error" class="alert alert-error">{{ error }}</div>

        <form @submit.prevent="submit">
          <div class="form-section-title">Informations générales</div>

          <div class="form-group">
            <label>Modèle *</label>
            <input v-model="form.modele" type="text" placeholder="Ex: Ferrari 488 GTB" required />
          </div>

          <div class="form-group">
            <label>Marque *</label>
            <select v-model="form.marque" required>
              <option value="">— Choisir une marque —</option>
              <option v-for="m in marques" :key="m.id" :value="`/api/marques/${m.id}`">
                {{ m.nom }} · {{ m.pays }}
              </option>
            </select>
          </div>

          <div class="form-section-title" style="margin-top:1.5rem">Caractéristiques</div>

          <div class="form-row">
            <div class="form-group">
              <label>Prix (€) *</label>
              <input v-model="form.prix" type="number" min="0" step="1" placeholder="250000" required />
            </div>
            <div class="form-group">
              <label>Puissance (ch) *</label>
              <input v-model="form.puissance" type="number" min="1" placeholder="660" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Année *</label>
              <input v-model="form.annee" type="number" min="1886" max="2100" placeholder="2024" required />
            </div>
            <div class="form-group">
              <label>URL Photo</label>
              <input v-model="form.photo" type="url" placeholder="https://..." />
            </div>
          </div>

          <Transition name="page">
            <div v-if="form.photo" class="photo-preview">
              <img :src="form.photo" alt="Aperçu" @error="() => form.photo = ''" />
            </div>
          </Transition>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              <Icons :name="isEdit ? 'edit' : 'plus'" :size="16" />
              {{ submitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
            </button>
            <RouterLink class="btn btn-secondary" to="/vehicules">
              <Icons name="back" :size="16" /> Annuler
            </RouterLink>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import Icons from '../components/Icons.vue'

const props = defineProps({ id: String })
const router = useRouter()
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'

const isEdit   = computed(() => !!props.id)
const loading  = ref(true)
const submitting = ref(false)
const error    = ref('')
const marques  = ref([])
const form     = ref({ modele: '', marque: '', prix: '', puissance: '', annee: new Date().getFullYear(), photo: '' })

onMounted(async () => {
  try {
    const mRes = await fetch(`${API_URL}/api/marques`)
    marques.value = (await mRes.json())['hydra:member'] ?? []
    if (isEdit.value) {
      const vRes = await fetch(`${API_URL}/api/vehicules/${props.id}`)
      const v = await vRes.json()
      form.value = { modele: v.modele, marque: `/api/marques/${v.marque?.id}`, prix: v.prix, puissance: v.puissance, annee: v.annee, photo: v.photo ?? '' }
    }
  } catch { error.value = 'Erreur de chargement' }
  finally { loading.value = false }
})

async function submit() {
  error.value = ''; submitting.value = true
  const payload = { modele: form.value.modele, marque: form.value.marque, prix: String(form.value.prix), puissance: parseInt(form.value.puissance), annee: parseInt(form.value.annee), photo: form.value.photo || null }
  try {
    const res = await fetch(
      isEdit.value ? `${API_URL}/api/vehicules/${props.id}` : `${API_URL}/api/vehicules`,
      { method: isEdit.value ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/ld+json' }, body: JSON.stringify(payload) }
    )
    if (res.ok) router.push('/vehicules')
    else { const d = await res.json(); error.value = d['hydra:description'] ?? 'Erreur sauvegarde' }
  } catch { error.value = "Erreur de connexion" }
  finally { submitting.value = false }
}
</script>
