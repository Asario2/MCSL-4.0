<template>
    <button class="backbtn inline-flex  button-icon items-center px-1 py-1.5 md:px-2 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg button_text_case_bg mb-4"
            @click="goBack(url)" title="Zurück" alt="Zurück" aria-title="Zurück">
           <IconCircleLeft />
            Zurück zu{{ r }} <slot></slot>

    </button>
  </template>

  <script>
  import IconCircleLeft from "@/Application/Components/Icons/IconCircleLeft.vue"
  export default {
    name: "BackBtn",
   props:{

    url:{
        type:String,
        required: true,
    },
    r:{
        type:String,
        default:'',
    },
   },
    methods: {
      goBack(url) {
        location.href=url;
      },
    },
    components:{
        IconCircleLeft
    }
  };
  </script>

  <style scoped>
  .backbtn {
    display: inline-flex;
    align-items: center;
    gap: 6px;        /* Abstand zwischen Text und Icon */
    cursor: pointer;
    font-weight: 500;

    border: none;
    padding: 17px 4px;
    font-size: 0.9rem;
  }

  .backbtn i {
    font-size: 25px;
  }
  .button-icon{
    line-height: 27px;
    height:27px !important;
  }
  </style>

