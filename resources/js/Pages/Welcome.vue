<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
});

onMounted(() => {
  // Hero Section Animations
  gsap.from(".hero-text", {
    opacity: 0,
    y: 50,
    duration: 1.2,
    ease: "power3.out",
    stagger: 0.2,
  });

  gsap.from(".hero-image", {
    opacity: 0,
    x: 100,
    duration: 1.5,
    ease: "power3.out",
    delay: 0.3,
  });

  // Feature Cards Animation
  gsap.utils.toArray(".feature-card").forEach((card, index) => {
    gsap.from(card, {
      scrollTrigger: {
        trigger: card,
        start: "top 80%",
        end: "top 20%",
        toggleActions: "play none none none",
      },
      opacity: 0,
      y: 60,
      duration: 1,
      ease: "power2.out",
      delay: index * 0.1,
    });
  });

  // Video Section Animation
  gsap.from(".video-section", {
    scrollTrigger: {
      trigger: ".video-section",
      start: "top 80%",
      end: "top 20%",
    },
    opacity: 0,
    y: 40,
    duration: 1,
    ease: "power2.out",
  });
});
</script>

<template>
  <Head title="Welcome to Leoni" />

  <!-- Hero Section -->
  <section class="relative min-h-screen flex items-center bg-gradient-to-br from-gray-950 via-blue-950 to-purple-950 overflow-hidden">
    <div class="absolute inset-0 opacity-10 [mask-image:linear-gradient(180deg,white,rgba(255,255,255,0))]">
      <div class="absolute inset-0 bg-[radial-gradient(#22d3ee_1px,transparent_1px)] [background-size:20px_20px]"></div>
    </div>

    <div class="relative container mx-auto px-6 py-24 flex flex-col lg:flex-row items-center gap-12">
      <div class="lg:w-1/2 space-y-8 hero-text">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent leading-tight">
          Next-Gen Service Management <span class="text-white">Reimagined</span>
        </h1>
        <p class="text-lg sm:text-xl text-gray-200 font-light max-w-xl">
          Transform your service operations .
        </p>
        <div v-if="canLogin" class="flex gap-4">
          <Link
            :href="route('login')"
            class="relative bg-cyan-500 hover:bg-cyan-600 px-8 py-3 rounded-full font-semibold text-white transition-all hover:shadow-xl hover:-translate-y-1 group overflow-hidden"
          >
            <span class="relative z-10">Get Started →</span>
            <span class="absolute inset-0 bg-cyan-700 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
          </Link>
        </div>
      </div>

      <div class="lg:w-1/2 hero-image">
        <div class="relative group">
          <div class="absolute -inset-2 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition duration-500"></div>
          <div class="relative bg-gray-800/50 backdrop-blur-md rounded-3xl p-6 shadow-2xl transform transition-all hover:scale-[1.03] border border-gray-700/50">
            <img src="/images/hero-illustration.svg" alt="Dashboard" class="w-full h-auto rounded-2xl" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Grid -->
  <section class="py-24 bg-gray-100 dark:bg-gray-900">
    <div class="container mx-auto px-6">
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
          Why Leoni Stands Out
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mt-4">
          Enterprise-grade capabilities meet intuitive design
        </p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(service, index) in services"
          :key="index"
          class="feature-card group bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700"
        >
          <div class="w-12 h-12 mb-4 rounded-lg bg-cyan-500/10 flex items-center justify-center group-hover:bg-cyan-500/20 transition-colors">
            <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-white">{{ service.title }}</h3>
          <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ service.description }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Video Guide Section -->
  <section class="py-24 bg-gradient-to-br from-gray-900 to-blue-950">
    <div class="container mx-auto px-6">
      <div class="max-w-3xl mx-auto text-center video-section">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">See Leoni in Action</h2>
        <p class="text-lg text-gray-200 mb-8 max-w-xl mx-auto">
          Watch this short video to understand how Leoni can transform your service management experience.
        </p>
        <div class="relative w-full aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-2xl">
          <iframe
            class="w-full h-full"
            src="https://www.youtube.com/embed/1GYfY9lnUOg"
            title="Leoni Product Demo"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      services: [

        {
          title: "Real-Time Analytics",
          description: "Interactive dashboards with predictive insights and automated reporting."
        },
        {
          title: "Smart Scheduling",
          description: "Intelligent resource allocation and calendar optimization."
        },
        {
          title: "Enterprise Security",
          description: "Military-grade encryption with SOC 2 Type II compliance."
        },
        {
          title: "Global Support",
          description: "24/7 support with average response time under 15 minutes."
        },

      ],
    };
  },
};
</script>

<style>
/* GSAP Animations Handled in Script */
.bg-gradient-to-br {
  background-attachment: fixed;
}

@media (prefers-reduced-motion: reduce) {
  .group,
  .hover\:scale-\[1\.03\]:hover,
  .hover\:shadow-xl:hover,
  .hover\:-translate-y-1:hover {
    transition: none !important;
    transform: none !important;
  }
}

/* Ensure smooth scrolling for anchor links */
html {
  scroll-behavior: smooth;
}

/* Video iframe responsiveness */
.aspect-w-16 {
  position: relative;
  width: 100%;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.aspect-w-16 iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>