<template>
  <div>
    <nav :class="{ scrolled: isScrolled }">
      <RouterLink class="brand" to="/">
        <Icons name="car" :size="24" color="#e94560" />
        SportCars
      </RouterLink>
      <RouterLink to="/vehicules">Véhicules</RouterLink>
      <RouterLink to="/marques">Marques</RouterLink>
    </nav>

    <RouterView v-slot="{ Component }">
      <Transition name="page" mode="out-in">
        <component :is="Component" />
      </Transition>
    </RouterView>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import Icons from './components/Icons.vue'

const isScrolled = ref(false)
function onScroll() { isScrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>
