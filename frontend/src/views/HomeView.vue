<template>
  <div>
    <!-- ── Hero parallax ────────────────── -->
    <section class="hero" ref="heroRef">
      <div class="hero-grid" data-parallax="0.15" ref="gridRef"></div>
      <div class="hero-glow" data-parallax="0.3" ref="glowRef"></div>

      <div class="hero-content" data-parallax="-0.05" ref="contentRef">
        <div class="hero-eyebrow">
          <span class="eyebrow-line"></span>
          WR406D · Collection
          <span class="eyebrow-line"></span>
        </div>
        <h1 class="hero-title">
          <span class="title-line">Véhicules</span>
          <span class="title-line accent">de Sport</span>
        </h1>
        <p class="hero-sub">Gérez, explorez et découvrez les plus belles supercars du monde.</p>
        <div class="hero-cta">
          <RouterLink class="btn btn-primary" to="/vehicules">
            <Icons name="car" :size="16" color="white" /> Explorer la flotte
          </RouterLink>
          <RouterLink class="btn btn-secondary" to="/marques">
            <Icons name="tag" :size="16" /> Les marques
          </RouterLink>
        </div>
      </div>

      <div class="hero-car-wrap" data-parallax="0.25" ref="carRef">
        <div class="hero-car-glow"></div>
        <Icons name="car" :size="320" color="rgba(233,69,96,0.12)" class="hero-car-bg" />
        <Icons name="car" :size="220" color="rgba(233,69,96,0.7)" class="hero-car-fg" />
      </div>

      <div class="hero-scroll-hint">
        <span>scroll</span>
        <div class="scroll-line"></div>
      </div>
    </section>

    <!-- ── Stats ────────────────────────── -->
    <section class="stats-section page" ref="statsRef">
      <div class="stats-grid">
        <div class="stat-card glass" v-for="(s, i) in stats" :key="i" :style="`transition-delay:${i*0.12}s`" :class="{ visible: statsVisible }">
          <div class="stat-icon">
            <Icons :name="s.icon" :size="28" color="#e94560" />
          </div>
          <div class="stat-number" :ref="el => counterRefs[i] = el">0</div>
          <div class="stat-label">{{ s.label }}</div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import Icons from '../components/Icons.vue'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'

/* refs DOM */
const heroRef = ref(null)
const gridRef = ref(null)
const glowRef = ref(null)
const contentRef = ref(null)
const carRef = ref(null)
const statsRef = ref(null)
const counterRefs = reactive([])
const statsVisible = ref(false)

const stats = ref([
  { icon: 'car', label: 'Véhicules', value: 0 },
  { icon: 'tag', label: 'Marques',   value: 0 },
  { icon: 'gauge', label: 'Ch max',  value: 0 },
  { icon: 'euro', label: 'Prix max (k€)', value: 0 },
])

/* ─ Parallax ─────────────────────────── */
function applyParallax(y) {
  const layers = [
    { el: gridRef.value,    speed: 0.15 },
    { el: glowRef.value,    speed: 0.3  },
    { el: contentRef.value, speed: -0.05 },
    { el: carRef.value,     speed: 0.25 },
  ]
  layers.forEach(({ el, speed }) => {
    if (el) el.style.transform = `translateY(${y * speed}px)`
  })
}
function onScroll() { applyParallax(window.scrollY) }

/* ─ Counter animation ────────────────── */
function animateCounter(el, target, suffix = '') {
  if (!el) return
  const duration = 1600
  const start = performance.now()
  function tick(now) {
    const t = Math.min((now - start) / duration, 1)
    const eased = 1 - Math.pow(1 - t, 4)
    el.textContent = Math.round(target * eased) + suffix
    if (t < 1) requestAnimationFrame(tick)
  }
  requestAnimationFrame(tick)
}

/* ─ Intersection observer (stats) ───── */
let statsObserver
function observeStats() {
  statsObserver = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) {
      statsVisible.value = true
      stats.value.forEach((s, i) => {
        setTimeout(() => animateCounter(counterRefs[i], s.value), i * 120)
      })
      statsObserver.disconnect()
    }
  }, { threshold: 0.3 })
  if (statsRef.value) statsObserver.observe(statsRef.value)
}

/* ─ Fetch data ───────────────────────── */
onMounted(async () => {
  window.addEventListener('scroll', onScroll, { passive: true })

  try {
    const [vRes, mRes] = await Promise.all([
      fetch(`${API_URL}/api/vehicules`),
      fetch(`${API_URL}/api/marques`)
    ])
    const vData = await vRes.json()
    const mData = await mRes.json()
    const vehicules = vData['hydra:member'] ?? []
    const marques   = mData['hydra:member'] ?? []

    stats.value[0].value = vehicules.length
    stats.value[1].value = marques.length
    stats.value[2].value = Math.max(...vehicules.map(v => v.puissance ?? 0), 0)
    stats.value[3].value = Math.round(Math.max(...vehicules.map(v => parseFloat(v.prix) ?? 0), 0) / 1000)
  } catch (_) {}

  observeStats()
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  statsObserver?.disconnect()
})
</script>

<style scoped>
/* ── Hero ── */
.hero {
  position: relative;
  height: 100vh;
  min-height: 600px;
  display: flex;
  align-items: center;
  overflow: hidden;
  isolation: isolate; /* contient tous les z-index enfants */
}

.hero-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(233,69,96,0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(233,69,96,0.06) 1px, transparent 1px);
  background-size: 60px 60px;
  will-change: transform;
}
.hero-glow {
  position: absolute;
  top: 10%; right: 5%;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(233,69,96,0.15) 0%, transparent 70%);
  border-radius: 50%;
  will-change: transform;
  pointer-events: none;
}

.hero-content {
  position: relative;
  z-index: 2;
  padding: 0 2.5rem;
  max-width: 680px;
  margin-left: max(2.5rem, calc(50vw - 640px));
  will-change: transform;
}

.hero-eyebrow {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 1.5rem;
}
.eyebrow-line {
  flex: 1; max-width: 40px;
  height: 1px;
  background: var(--accent);
}

.hero-title {
  font-family: 'Orbitron', sans-serif;
  font-size: clamp(2.8rem, 6vw, 5.5rem);
  font-weight: 900;
  line-height: 1.05;
  letter-spacing: -1px;
  margin-bottom: 1.5rem;
}
.title-line { display: block; }
.title-line.accent {
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-sub {
  font-size: 1.05rem;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 2.5rem;
  max-width: 480px;
}

.hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; }

.hero-car-wrap {
  position: absolute;
  right: max(2rem, calc(50vw - 640px));
  display: flex;
  align-items: center;
  justify-content: center;
  will-change: transform;
  pointer-events: none;
}
.hero-car-bg {
  position: absolute;
  animation: float 6s ease-in-out infinite;
  filter: blur(2px);
}
.hero-car-fg {
  position: relative;
  animation: float 6s ease-in-out infinite;
  filter: drop-shadow(0 0 40px rgba(233,69,96,0.5));
}
.hero-car-glow {
  position: absolute;
  width: 300px; height: 150px;
  background: radial-gradient(ellipse, rgba(233,69,96,0.3), transparent 70%);
  bottom: -20px;
  filter: blur(20px);
  animation: pulse-glow 4s ease-in-out infinite;
}

.hero-scroll-hint {
  position: absolute;
  bottom: 2.5rem; left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.65rem;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: var(--muted);
}
.scroll-line {
  width: 1px; height: 40px;
  background: linear-gradient(to bottom, var(--accent), transparent);
  animation: scrollAnim 1.8s ease-in-out infinite;
}
@keyframes scrollAnim {
  0%   { transform: scaleY(0); transform-origin: top; }
  50%  { transform: scaleY(1); transform-origin: top; }
  51%  { transform: scaleY(1); transform-origin: bottom; }
  100% { transform: scaleY(0); transform-origin: bottom; }
}

/* ── Stats ── */
.stats-section { padding-top: 0; }
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}
.stat-card {
  padding: 2rem 1.5rem;
  border-radius: var(--radius);
  text-align: center;
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.5s, transform 0.5s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
}
.stat-card.visible { opacity: 1; transform: translateY(0); }
.stat-card:hover { border-color: rgba(233,69,96,0.3); }
.stat-number {
  font-family: 'Orbitron', sans-serif;
  font-size: 2.8rem;
  font-weight: 900;
  color: var(--accent);
  line-height: 1;
  min-height: 3.2rem;
}
.stat-label {
  font-size: 0.75rem;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
}
</style>
