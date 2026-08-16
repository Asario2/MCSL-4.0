<template>
    <div
        class="relative overflow-hidden w-[348px] md:w-full"
    >

        <!-- Hintergrund -->
        <div
            ref="bg"
            class="absolute top-0 left-0 h-full"
            style="
                width: 4225px;
                background-image: url('/images/logos/pna_bg.jpg');
                background-repeat: repeat-x;
                background-size: auto 100%;
                background-position: left center;
            "
        ></div>

        <!-- Logo -->
        <img
            id="pna_logo"
            :class="[
                small ? 'max-w-[348px] !h-[60px]' : '',
                'relative z-10 block w-[348px] max-w-none h-auto md:w-full md:h-[180px]'
            ]""
            :src="'/images/logos/pna_logo' + GetLogin() + '.png'"
            alt="Paul Nadler Logo"
        >




    </div>
</template>

<script>
export default {
    name: "PnaLogo",

    props: {
        ab: {
            type: String,
            default: '',
        },

        small: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            mode: "light",
            ab2: "",
            LL:'',
        };
    },

    mounted() {
        if (typeof window === "undefined") return;
        if(localStorage.getItem('theme') == 'light')
        {
            this.LL = "l";
        }
        this.mode =
            /\/(login|register)(\/|$)/i.test(window.location.pathname)
                ? "light"
                : (localStorage.getItem("theme") || "light");

        this.ab2 = this.GetLogin();


        import("gsap").then(({ gsap }) => {

            const bg = this.$refs.bg;

            if (!bg) return;

            const timeline = gsap.timeline({
                repeat: -1
            });

            timeline.to(bg, {
                x: "-50%",
                duration: 90,
                ease: "none",
            });

            timeline.to({}, {
                duration: 30
            });

            timeline.to(bg, {
                x: "0%",
                duration: 90,
                ease: "none",
            });

            timeline.to({}, {
                duration: 30
            });

        });
    },

    methods: {

        GetLogin() {

            if (typeof window === "undefined") {
                return "";
            }

            const url = location.href;

            if (
                !url.includes("/login") &&
                !url.includes("/forgot-password") &&
                !url.includes("/register") &&
                !url.includes("/email/verify") &&
                !url.includes("reset-password") &&
                !url.includes("/confirm-password") &&
                !url.includes("/verify-email") &&
                this.mode === "dark"
            ) {
                return "";
            }

            return "l";
        },

    },
};
</script>
