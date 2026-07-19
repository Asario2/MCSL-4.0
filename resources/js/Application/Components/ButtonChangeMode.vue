```vue
<template>
    <button type="button" @click="changeMode" class="flex items-center flex-nowrap cursor-pointer rounded-md px-4 py-2 border border-transparent leading-4 font-medium bg-transparent transition focus:outline-none">
        <IconNight v-if="mode==='light'" class="w-4 h-4"/>
        <IconSun v-else class="w-4 h-4"/>
    </button>
</template>

<script>
import IconNight from "@/Application/Components/Icons/Night.vue";
import IconSun from "@/Application/Components/Icons/Sun.vue";

export default {
    name:"ButtonChangeMode",

    components:{IconNight,IconSun},

    props:{
        mode:{type:String},
        class:{
            type:String,
            default:"cursor-pointer inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-layout-sun-100 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-layout-night-100 dark:hover:text-layout-night-900"
        }
    },

    emits:["changeMode"],

    methods:{
        applyTheme(mode){
            if(
                window.location.pathname==='/login'
                || window.location.pathname==='/register'
            ){
                document.documentElement.classList.remove('dark');
                return;
            }

            document.documentElement.classList.toggle(
                'dark',
                mode==='dark'
            );
        },

        changeMode(){

            const newMode=
                this.mode==='dark'
                    ? 'light'
                    : 'dark';

            localStorage.setItem(
                'theme',
                newMode
            );

            this.applyTheme(
                newMode
            );

            this.$emit(
                'changeMode',
                newMode
            );
            document.getElementById("pna_logo")?.setAttribute(
                "src",
                `/images/logos/pna_logo_${newMode}.png`
            );
            fetch('/toggle-dark-mode',{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':document.getElementById('token').value,
                    'Content-Type':'application/json'
                },
                body:JSON.stringify({
                    dark_mode:newMode
                })

            }).catch(console.error);

            document.querySelectorAll('.ai-button')
                .forEach(el=>{
                    el.src=`/images/icons/ai-${newMode}.png`;
                });

            document.querySelectorAll('#ai-image')
                .forEach(el=>{
                    el.src=`/images/_ab/ai-teaser-${newMode}.jpg`;
                });

            this.reloadQrCode();
        },

        reloadQrCode(){
            fetch(location.href)
                .then(r=>r.text())
                .then(html=>{
                    const doc=new DOMParser().parseFromString(html,'text/html');
                    const newSvg=doc.querySelector('svg#QrCode');
                    const currentSvg=document.querySelector('svg#QrCode');

                    if(newSvg&&currentSvg){
                        currentSvg.innerHTML=newSvg.innerHTML;
                    }
                })
                .catch(console.error);
        }
    }
};
</script>
```
