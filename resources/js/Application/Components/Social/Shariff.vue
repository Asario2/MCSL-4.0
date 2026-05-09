<template>
  <div ref="shariff"></div>
</template>

<script>
export default {
  name: "ShariffModul",

  props: {
    config: {
      type: Object,
      default: () => ({})
    }
  },

  async mounted() {
    // 🧯 nur im Browser
//     if (typeof window === "undefined") return;

//     const { default: Shariff } = await import("shariff");

//     new Shariff(this.$refs.shariff, this.config);
  }
};
</script>
<style>
.shariff a {
  display: flex;
  align-items: center;
  gap: 6px;
}

.shariff .share_text {
  display: none; /* versteckt "teilen" */
}
.shariff .shariff-text,
.shariff .share_text,
.shariff .shariff-heading {
    display: none !important;
}
.shariff .shariff-button {
    border-radius: 6px;
    padding: 0;
    overflow: hidden;
}
.shariff .shariff-button.facebook .share-icon {
  background: #1877f2 !important;
}
.shariff .facebook-f     .fab {
  background: #1877f2 !important;
  color: white !important;
  border-radius: 4px;
  padding: 6px;
}

.shariff .telegram .fab {
  background: #2aabee !important;
  color: white !important;
  border-radius: 4px;
  padding: 6px;
}

.shariff .whatsapp .fab {
  background: #25d366 !important;
  color: white !important;
  border-radius: 4px;
  padding: 6px;
}

.shariff .xing .fab {
  background: #006567 !important;
  color: white !important;
  border-radius: 4px;
  padding: 6px;
}
.shariff .facebook .share-icon { background: #1877f2 !important; }
.shariff .twitter .share-icon { background: #1da1f2 !important; }
.shariff .whatsapp .share-icon { background: #25d366 !important; }
.shariff .linkedin .share-icon { background: #0a66c2 !important; }
.shariff .telegram .share-icon { background: #2aabee !important; }
.shariff .mail .share-icon { background: #444 !important; }
</style>
