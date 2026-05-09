<template>
  <div class="relative w-full h-[70px] overflow-hidden">
    <!-- Hintergrundbild -->
    <div
      class="absolute top-0 left-0 w-[200%] h-full bg-repeat-x bg-contain"
      ref="bg"
      :style="{ backgroundImage: 'url(/images/logos/mfx_logo_bg.png)' }"
    ></div>

    <!-- Vordergrundbild -->
    <img
      class="relative z-10 h-full mx-auto"
      :src="'/images/logos/mfx_logo_bh' + ab + '.png'"
      alt="Logo"
    />
  </div>
</template>

<script>
import { gsap } from 'gsap';

export default {
    name: "mfxlogo",
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

