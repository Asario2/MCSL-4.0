    <template>
    <div class="vcard">
        <address class="mt-2">
        <div class="subheader">
            <b>{{ rumLaut(data.name) }}</b><br />
            <span class="adr">

            {{ rumLaut(data.strasse) }}<br />
            <span class="postalCode">{{ rumLaut(data.plz) }}</span>&nbsp;
            <span class="locality">{{ rumLaut(data.ort) }}</span>
            </span>

            <!-- Telefon -->
            <div v-if="data.festnetz">
            {{ lang.phone }}:
            <a :href="'tel:' + data.festnetz">{{ data.festnetz }}</a>
            </div>

            <!-- Mobil -->
            <div v-if="data.mobil">
            {{ lang.mobil }}:
            <a :href="'tel:' + data.mobil">{{ data.mobil }}</a>
            </div>

            <!-- Fax -->
            <div v-if="data.fax">
            {{ lang.fax }}: {{ data.fax }}
            </div>

            <!-- Email -->sad
            <div v-if="data.email">
            {{ lang.email }}:
            <a :href="'mailto:' + data.email">{{ hideEmail(data.email) }}</a>
            </div>
        </div>
        </address>
    </div>
    </template>

<script>
import {rumLaut} from "@/helpers";
export default {
  name: "ContactCard",



  props: {
    data: {
      type: Object,
      required: true,
      default: () => ({})
    },
    lang: {
      type: Object,
      default: () => ({
        phone: "Telefon",
        mobil: "Mobil",
        fax: "Fax",
        email: "E-Mail"
      })
    }
  },

  methods: {
    rumLaut,
    // einfache Ersatzfunktion für PHP gen_hidemail()
    hideEmail(email) {
      if (!email) return "";
      return email.replace(/@/, " [at] ");
    }
  }
};
</script>

<style scoped>
.vcard {
  padding: 10px;
}
.subheader {
  line-height: 1.5;
}
address {
  font-style: normal;
}
</style>
