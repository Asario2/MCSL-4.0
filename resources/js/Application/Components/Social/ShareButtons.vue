        <template>
        <div class="share-wrapper" style="z-index:1000 !important">
        <a
            v-for="item in items"
            :key="item.name"
            @click.prevent="openShare(item.url)"
            class="share-button"
            :class="item.name"
            :title="item.label"
        >
            <component :is="item.component" class="w-5 h-5"/>
        </a>
        </div>
        </template>

        <script>
        import IconFacebook from "@/Application/Components/Icons/IconFacebook.vue";
        import IconTelegram from "@/Application/Components/Icons/IconTelegram.vue";
        import IconWhatsapp from "@/Application/Components/Icons/IconWhatsapp.vue";
        import IconXing from "@/Application/Components/Icons/IconXing.vue";
        import IconX from "@/Application/Components/Icons/IconX.vue";
        export default {
        name: "ShareButtons",

        components:
        {
            IconFacebook,
            IconTelegram,
            IconWhatsapp,
            IconXing,
            IconX,
        },
        props: {
            url: {
            type: String,
            required: true
            },
            title: {
            type: String,
            default: ""
            }
        },

        computed: {
            encodedUrl() {
            return encodeURIComponent(this.url);
            },

            encodedTitle() {
            return encodeURIComponent(this.title);
            },

            items() {
                return [
                    {
                        name:"facebook",
                        label:"Facebook",
                        component:"IconFacebook",
                        url:`https://www.facebook.com/sharer/sharer.php?u=${this.encodedUrl}`
                    },
                    {
                        name:"telegram",
                        label:"Telegram",
                        component:"IconTelegram",
                        url:`https://t.me/share/url?url=${this.encodedUrl}&text=${this.encodedTitle}`
                    },
                    {
                        name:"whatsapp",
                        label:"WhatsApp",
                        component:"IconWhatsapp",
                        url:`https://api.whatsapp.com/send?text=${this.encodedTitle}%20${this.encodedUrl}`
                    },
                    {
                        name:"xing",
                        label:"XING",
                        component:"IconXing",
                        url:`https://www.xing.com/spi/shares/new?url=${this.encodedUrl}`
                    },
                    {
                        name:"x",
                        label:"X",
                        component:"IconX",
                        url:`https://twitter.com/intent/tweet?url=${this.encodedUrl}&text=${this.encodedTitle}`
                    }
                ];
            }
        },
            methods: {
            openShare(url) {
            const width = 680;
            const height = 900;

            const left = window.screenX + (window.outerWidth - width) / 2;
            const top = window.screenY + (window.outerHeight - height) / 2;

            window.open(
                url,
                "shareWindow",
                `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`
            );
            }
        }

        };
        </script>
        <style>
        .share-wrapper {
        display: flex;
        gap: 8px;
        z-index:10000 !important;
        }

        .share-button {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 6px;
        color: white !important;
        text-decoration: none;
        transition: transform 0.15s ease;
        }

        .share-button:hover {
        transform: scale(1.05);
        }

        /* Farben */
        .share-button.facebook { background: #1877f2; }
        .share-button.telegram { background: #2aabee; }
        .share-button.whatsapp { background: #25d366; }
        .share-button.xing { background: #006567; }
        .share-button.x { background: #000; }
        .share-button i {
        font-size: 16px;

        color: #fff !important;
        }
        .fab .fa-x-twitter:before {
                content: "\e61b" !important;
                }

        </style>
