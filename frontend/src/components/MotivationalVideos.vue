<script setup>
const videos = [
  {
    id: 'wI2K0p5_NCU',
    title: 'Animasi Edukasi STOP Bullying!',
    description: 'Sebuah video animasi singkat yang menjelaskan dampak perundungan dan cara menghentikannya.',
  },
  {
    id: 'ZJgiPlbIf8w',
    title: 'Cara Belajar Efektif - Kenali Gaya Belajarmu!',
    description: 'Temukan cara belajar yang paling sesuai untukmu agar lebih semangat dan mudah paham.',
  },
  {
    id: 'f9O3U57nF5g',
    title: 'Jangan Takut Gagal | Video Motivasi',
    description: 'Sebuah pengingat bahwa kegagalan adalah bagian dari proses belajar untuk menjadi lebih baik.',
  },
];
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Video Motivasi Untukmu</h2>
    <p class="text-gray-600 dark:text-gray-400 mb-6">Tonton video ini untuk menambah semangat dan wawasan baru!</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div 
        v-for="video in videos" 
        :key="video.id" 
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 transform hover:-translate-y-1 transition-transform duration-300"
      >
        <div class="aspect-w-16 aspect-h-9">
          <iframe 
            :src="`https://www.youtube.com/embed/${video.id}`" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
            allowfullscreen
            class="w-full h-full"
          ></iframe>
        </div>
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ video.title }}</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">{{ video.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}
.aspect-h-9 {
  height: 0;
}
.aspect-w-16 iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>