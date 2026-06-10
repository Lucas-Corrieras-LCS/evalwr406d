<template>
  <div class="page">
    <div class="page-header">
      <h1>
        <Icons :name="isEdit ? 'edit' : 'plus'" :size="26" color="#e94560" />
        {{ isEdit ? 'Modifier la marque' : 'Nouvelle marque' }}
      </h1>
      <RouterLink class="btn btn-secondary" to="/marques">
        <Icons name="back" :size="16" /> Retour
      </RouterLink>
    </div>

    <div v-if="loading" class="empty-state"><p>Chargement...</p></div>

    <div v-else class="form-wrapper">
      <div class="form-glass">
        <div v-if="error" class="alert alert-error">{{ error }}</div>

        <form @submit.prevent="submit">
          <div class="form-section-title">Identité de la marque</div>

          <div class="form-group">
            <label>Nom *</label>
            <input v-model="form.nom" type="text" placeholder="Ex: Ferrari" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Année de création *</label>
              <input v-model="form.anneeCreation" type="number" min="1800" max="2100" placeholder="1947" required />
            </div>
            <div class="form-group">
              <label>Pays d'origine *</label>
              <input v-model="form.pays" type="text" placeholder="Ex: Italie" required />
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              <Icons :name="isEdit ? 'edit' : 'plus'" :size="16" />
              {{ submitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer') }}
            </button>
            <RouterLink class="btn btn-secondary" to="/marques">
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

const isEdit    = computed(() => !!props.id)
const loading   = ref(isEdit.value)
const submitting = ref(false)
const error     = ref('')
const form      = ref({ nom: '', anneeCreation: 2000, pays: '' })

onMounted(async () => {
  if (!isEdit.value) return
  try {
    const m = await (await fetch(`${API_URL}/api/marques/${props.id}`)).json()
    form.value = { nom: m.nom, anneeCreation: m.anneeCreation, pays: m.pays }
  } catch { error.value = 'Erreur de chargement' }
  finally { loading.value = false }
})

async function submit() {
  error.value = ''; submitting.value = true
  const payload = { nom: form.value.nom, anneeCreation: parseInt(form.value.anneeCreation), pays: form.value.pays }
  try {
    const res = await fetch(
      isEdit.value ? `${API_URL}/api/marques/${props.id}` : `${API_URL}/api/marques`,
      { method: isEdit.value ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/ld+json' }, body: JSON.stringify(payload) }
    )
    if (res.ok) router.push('/marques')
    else { const d = await res.json(); error.value = d['hydra:description'] ?? 'Erreur sauvegarde' }
  } catch { error.value = 'Erreur de connexion' }
  finally { submitting.value = false }
}
</script>
