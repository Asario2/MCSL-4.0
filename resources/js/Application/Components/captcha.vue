<template>
    <div v-if="!$page?.props?.auth?.user">
      <img
        :src="captchaUrl"
        alt="CAPTCHA"
        width="200"
        height="70"
        @click="refreshCaptcha"
        style="cursor:pointer"
        class="mt-[8px]"
      >
      <input
        type="text"
        name="captcha"
        id="captcha"
        class="block p-2 mb-[8px] mt-[8px] dark:placeholder:text-layout-sun-200  w-full text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75 bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200 selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
        maxlength="6"
        style="width:200px;"
        v-model="captchaInput"
        @input="emitValue"
        placeholder="Captcha"
      />
    </div>
  </template>

  <script>
  export default {
    name: "captcha",
    data() {
      return {
        captchaInput: '',
        timestamp: Date.now()
      }
    },
    computed: {
      captchaUrl() {
        return `/images/captcha.php?time=${this.timestamp}`
      }
    },
    methods: {
      emitValue() {
        this.$emit('update', this.captchaInput)
      },
      refreshCaptcha() {
        this.timestamp = Date.now() // neues Bild erzwingen
      },
      resetCaptcha() {
        this.captchaInput = ''      // Input leeren
        this.refreshCaptcha()       // Bild erneuern
        this.emitValue()            // Main-Komponente updaten
      }
    }
  };
  </script>


<style scoped>
.bgo{
    background-color: rgb(39 39 42);
}
</style>

