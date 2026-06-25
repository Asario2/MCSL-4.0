<template>
  <div class="relative w-full h-[180px] overflow-hidden">
    <!-- Hintergrundbild -->
    <div
      class="absolute top-0 left-0 w-[200%] h-full bg-repeat-x bg-contain"
      ref="bg"
      :style="{ backgroundImage: 'url(/images/logos/pna_bg.jpg)' }"
    ></div>

    <!-- Vordergrundbild -->
    <img
      class="relative z-10 mx-auto !h-[180px]"
      height="18ß"
      :src="'/images/logos/pna_logo.png'"
      alt="Logo"
    />
  </div>
</template>

<script>
import { gsap } from 'gsap';

export default {
    name: "PnaLogo",
    props:{
        ab:{
            default:'',
            type:String

        }
    },
  mounted() {
  if (typeof window === "undefined") return;

  import('gsap').then(({ gsap }) => {
    const bg = this.$refs.bg;

    const timeline = gsap.timeline({ repeat: -1 });

    timeline.to(bg, {
      x: '-50%',
      duration: 20,
      ease: 'power1.inOut',
    });

    timeline.to({}, { duration: 30 });

    timeline.to(bg, {
      x: '0%',
      duration: 20,
      ease: 'power1.inOut',
    });

    timeline.to({}, { duration: 30 });
  });
}
};
</script>

