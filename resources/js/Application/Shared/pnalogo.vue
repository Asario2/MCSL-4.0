<template>
  <div
  :class="[
        'relative w-full overflow-hidden',
        small ? 'h-[70px]' : 'md:h-[180px]'
    ]">
    <!-- Hintergrundbild -->
<div
    ref="bg"
    class="absolute top-0 left-0 h-full"
    :style="{
        width: '4225px',
        backgroundImage: 'url(/images/logos/pna_bg.jpg)',
        backgroundRepeat: 'repeat-x',
        backgroundSize: 'auto 100%',
        backgroundPosition: 'left center'
    }"
/>

    <!-- Vordergrundbild -->
    <img
        :class="[
            'relative z-10 mx-auto',
            small
                ? 'h-[70px] w-auto'
                : 'h-auto w-full md:w-auto md:h-[180px]'
        ]"
        id="pna_logo"
        :src="'/images/logos/pna_logo_'+ mode +'.png'"
        alt="Paul Nadler Logo"
    />
  </div>
</template>

<script>
import { get } from 'jquery';

// import { gsap } from 'gsap';

export default {
    name: "PnaLogo",
    props:{
        ab:{
            default:'',
            type:String

        },
        small: {
            type: Boolean,
            default: false,
        },

    },
    data()
    {
            this.mod = /\/(login|register)(\/|$)/i.test(window.location.pathname)
    ? 'light'
    : localStorage.theme;
                  this.mode =  this.mod ??  "";
    },
    mounted() {
        if (typeof window === "undefined") return;


        import("gsap").then(({ gsap }) => {
            const bg = this.$refs.bg;

            const timeline = gsap.timeline({ repeat: -1 });

            timeline.to(bg, {
                x: "-50%",
                duration: 90,
                ease: "none",
            });

            timeline.to({}, { duration: 30 });

            timeline.to(bg, {
                x: "0%",
                duration: 90,
                ease: "none",
            });

            timeline.to({}, { duration: 30 });
        });
    }
};
</script>

