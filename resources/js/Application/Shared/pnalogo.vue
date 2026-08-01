<template>
  <div class="w-full flex justify-center">
  <div
  :class="[
        'relative w-full overflow-hidden max-w-6xl',
        small ? 'h-[60px] ml-2' : 'md:h-[180px] '
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
       class="relative z-10 w-full"
    :class="small ? 'h-[60px]' : 'h-auto md:h-[180px]'"
        id="pna_logo"
        :src="'/images/logos/pna_logo'+ ab2 +'.png'"
        alt="Paul Nadler Logo"

    />
  </div>
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
        return{
            mode: "light",
            ab2: "",
        }
    },

    mounted() {
    if (typeof window === "undefined") return "dark";

        this.mode =
            /\/(login|register)(\/|$)/i.test(window.location.pathname)
                ? "light"
                : (localStorage.getItem("theme") || "light");

        this.ab2 = this.GetLogin();

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
    },
    methods:{
GetLogin()
    {
        if (typeof window === "undefined") {
            return "";
        }
        const url = location.href;
        if((!url.includes("/login") && !url.includes("/forgot-password") &&
        !url.includes("/register") && !url.includes("/email/verify") &&
        !url.includes("reset-password") && !url.includes("/confirm-password") &&
        !url.includes("/verify-email")) && this.mode=='dark')
        {
            return "";
        }
        return "l";
    }
    },
};
</script>

