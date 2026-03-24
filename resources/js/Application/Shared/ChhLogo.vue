<template>
    <!-- Vordergrundbild -->
    <div class="relative inline-block">

  <!-- Unsichtbares Klickfeld -->
  <a
    href="/admin/dashboard"
    class="absolute top-0 left-0 w-[50px] h-[50px] z-20 cursor-pointer"
  ></a>

  <!-- Dein Bild -->
  <img
    class="relative z-10 h-full mx-auto"
    :src="'/images/logos/chh.jpg'"
    alt="Logo"
  />

</div>

</template>

<script>
export default {
    name: "ChhLogo",

};
</script>

