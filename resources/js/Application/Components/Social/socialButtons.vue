<template>
    <div :class="['w-full h-full mt-2',big ? 'zi2' : '']" style="z-index:1000 !important;">
        <table :class="['w-full border-collapse mx-auto justify-center rounded-lg shadow-sm table-class', nostars ? 'NSMaTable' : 'MaTable']" style="max-width:300px;margin-bottom:0px;z-index:1000;" @click.stop>
            <tbody>
                <tr class="border-0">
                    <td class="text-center" width="60%">
                        <button @click.stop.prevent="openComments(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-button text-white hover:bg-button-600
                                       dark:bg-button-600 dark:hover:bg-button-700 dark:text-white text-center tog-tab"
                                :data-post-id="postId">
                            <icon-comment /> Kommentare
                        </button>
                    </td>

                    <td class="p-1.5 text-center" width="40%">
                        <button @click.stop.prevent="toggleShareBox(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-button text-white hover:bg-button-600
                                       dark:bg-button-600 dark:hover:bg-button-700 dark:text-white text-center tog-tab"
                                :data-post-id="postId">
                            <icon-share /> Teilen
                        </button>
                    </td>

                    <td class="text-center" v-if="!nostars">
                        <button @click.stop.prevent="toggleStarBox(postId)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg font-semibold bg-button text-white hover:bg-button-600
                                       dark:bg-button-600 dark:hover:bg-button-700 dark:text-white text-center tog-tab"
                                :data-post-id="postId">
                            <icon-star we="16" he="16" /> Bewerten
                        </button>
                    </td>
                </tr>

                <!-- Kommentarbox -->
                <tr v-if="showComments === postId">
                    <td colspan="3" class="p-4 h-auto align-top" style="z-index:1000 !important;" :id="'commentBox_' + postId">
                        <div class="w300 zi relative border border-gray-300 p-4 rounded-lg shadow-sm bg-layout-sun-100 dark:bg-layout-night-100">
                            <button
                                @click.stop.prevent="closeComments()"
                                class="absolute top-4 right-5 text-gray-500 hover:text-gray-700 text-xl font-bold z-30"
                                title="Kommentare schließen"
                            >
                                &times;
                            </button>
                            <br />

                            <Comments :postId="postId" :showComments="showComments" />
                        </div>
                    </td>
                </tr>

                <!-- Teilen -->
                <tr v-if="showShareBox[postId]">
                    <td colspan="3" class="p-4">
                        <!-- <div align="center" :ref="'shariff_' + postId" :added="urlAdded"
                             class="shariff w-full w300 relative border border-gray-300 p-4 pb-2 rounded-lg shadow-sm bg-white dark:bg-gray-800"
                             data-button-style="icon">
                        </div> -->
                        <ShareButtons
                            v-if="showShareBox[postId]"
                            :url="Murl"
                            :title="title"
                        />
                    </td>
                </tr>

                <!-- Bewertung -->
                <tr v-if="showStarBox[postId]">
                    <td colspan="3" class="p-4">
                        <RatingInput :postId="postId" :table="tablex" :name="name" @update-email="email = $event"/>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</template>

<script>
import { nextTick } from "vue";
import axios from "axios";
import ShareButtons from "@/Application/Components/Social/ShareButtons.vue";
import { Link } from "@inertiajs/vue3";
import Comments from "@/Application/Components/Social/comments.vue";
import RatingInput from "@/Application/Components/Social/RatingInput.vue";
import IconComment from "@/Application/Components/Icons/IconComment.vue";
import IconShare from "@/Application/Components/Icons/IconShare.vue";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import averageRating from "@/Application/Components/Social/averageratings.vue";
import { CleanTable } from '@/helpers';
import he from "he";
// import Shariff from 'shariff';

export default {
    name: "SocialButtons",
    components: {
        Link,
        Comments,
        RatingInput,
        IconComment,
        IconShare,
        ShareButtons,
        IconStar,
        averageRating,

    },
    props: {
        title:String,
        blog: Object,
        aiOverlayImage: String,
        postId: [Number, String],
        sm: String,
        nostars: { type: Boolean, default: false },
        slug: { type: String, default: "" },
        empty: { type: Boolean, default: false },
        sslug: { type: Boolean, default: false },
        xslug: { type: Boolean, default: false },
        ublock: String,
        sse:String,
        big:{Boolean,default:false},
         name:{
        type:String,
    },
    },
    data() {
        return {
            // dma: localStorage.getItem('theme'),
            urlAdded: this.urlAdder(this.postId),
            showShareBox: {},
            showStarBox: {},
            showComments: null,
             shariffLoaded: false
            // url2:'',
        };
    },
    computed:{
        Murl()
        {
            return location.href + this.urlAdded;
        }
    },
   async mounted() {
        this.urlAdded = this.urlAdder(this.postId);
//         this.imageRemove(this.postId);
//     if (typeof window === 'undefined') return;

//   import('shariff').then(({ default: Shariff }) => {
//     new Shariff(document.querySelector('.shariff'), {});
//   });
    },
    methods: {
        CleanTable,
        closeAllModals() {
            this.showComments = null;
            this.showShareBox = {};
            this.showStarBox = {};
        },

        openComments(id) {
            const wasOpen=this.showComments===id;
            this.closeAllModals();
            if(!wasOpen){
                this.imageRemove(id);
                this.showComments=id;
            }
            this.updateTeaserOverflow();
        },
        closeComments() {
            this.showComments = null;
            this.updateTeaserOverflow();
        },



        toggleStarBox(id) {
            const wasOpen=!!this.showStarBox[id];
            this.closeAllModals();
            if(!wasOpen) this.showStarBox[id]=true;
            this.updateTeaserOverflow();
        },
        toggleShareBox(id) {
            const wasOpen=!!this.showShareBox[id];
            this.closeAllModals();

            if(!wasOpen){
                this.showShareBox[id]=true;
                this.updateTeaserOverflow();
                nextTick(()=>this.initShariff(id));
            }else{
                this.updateTeaserOverflow();
            }
        },
      initShariff(id) {
//             nextTick(() => {
//                 const shariffRef = this.$refs['shariff_' + id];
//                 if (!shariffRef) return;
//                 this.ssez =  this.sse ? "?search=" + encodeURIComponent(this.sse) : this.urlAdded || '';

//                 const url = `${window.location.origin}${window.location.pathname}${this.ssez || ''}`;
//                 const url_alt = url.replace(CleanTable() + CleanTable() + "/",CleanTable()+"/");
// //                 console.log(url_alt);

//                 shariffRef.setAttribute('data-url', url_alt);
//                 new Shariff(shariffRef, {
//                     services: ["facebook", "telegram", "whatsapp", "xing", "twitter"],
//                     theme: "classic",
//                     orientation: "horizontal",
//                 });
//             });
         },
        toggleCommentBox(postId) {
            this.imageRemove(postId);
            this.showComments = this.showComments === postId ? null : postId;
        },
        imageRemove(id) {
            window.addEventListener('load', function() {
                const commentBox = document.getElementById("commentBox_" + id);
                if (!commentBox) return;
                const computedHeight = window.getComputedStyle(commentBox).height;
            });
        },
        decodeEntities(text) {
            if (!text) return "";
            text = text.replace(/<br\s*\/?>/g, "\n");
            return he.decode(text);
        },
        deleteDataRow(id) {
            if (confirm("Wollen Sie diesen Beitrag wirklich löschen?")) {
                axios.delete(this.routeDelete + id)
                    .then(() => {
                        this.$emit("deleted");
                        router.relaod();
                    })
                    .catch(err => console.error(err));
            }
        },
        editDataRow(id) {
            const rt = `/admin/tables/edit/${id}/images`;
            location.href = rt;
        },
        urlAdder(id) {
            // console.log('xslug:', this.xslug);
            // console.log('sslug:', this.sslug);
            // console.log('ublock:', this.ublock);
            // console.log('slug:', this.slug);
            if (this.ublock) return `/show/${this.ublock}/${id}`;
            if (this.empty) return "";
            if(this.sslug || this.xslug) return "#st"+id;
            return `/show/${this.slug}#st${id}`;
        },
        updateTeaserOverflow() {
            const ti = document.getElementById('teaser-img');
            if (!ti) return;
            const ti2 = document.getElementById('teaser-img2');
            // const anyOpen = Object.values(this.showShareBox).some(v => v) || Object.values(this.showStarBox).some(v => v) || this.showComments;
            // ti.style.overflow = anyOpen ? "hidden" : "visible";
            // if (ti2) ti2.style.overflow = anyOpen ? "hidden" : "visible";
        },
        reset() {
            if (this.form) this.form = Object.fromEntries(Object.keys(this.form).map(k => [k, null]));
        },
    },
    watch: {
    xslug() {
        this.urlAdded = this.urlAdder(this.postId);
    },
    sslug() {
        this.urlAdded = this.urlAdder(this.postId);
    },
    ublock() {
        this.urlAdded = this.urlAdder(this.postId);
    },
    slug() {
        this.urlAdded = this.urlAdder(this.postId);
    },
}

};

</script>

<style>
.w300 { max-width:300px; }
.shariff { margin-top:-11px; z-index:49; }
#commentBox { max-width:300px !important; width:300px !important; }

@media screen and (max-width:1000px){
    /* .MaTable{ margin-left:-58px; } */
    .SmMaTable{ margin-left:-7px; }
    .MaTable_blogs{ margin-left:-38px; }
    .SmMaTable_blogs{ margin-left:-38px; }
}
@media screen and (max-width:361px){ .MaTable{ margin-left:-22px !important; } }
@media screen and (min-width:1024px){ .Matable{ margin-left:0px; } }

/* Shariff Twitter Anpassungen */
.shariff .shariff-button.twitter,
.shariff .shariff-button.twitter a { background-color:#000 !important; color:#fff !important; }
.shariff .shariff-button.twitter:hover,
.shariff .shariff-button.twitter a:hover { background-color:#333 !important; color:#fff !important; }
.shariff .shariff-button.twitter .fa-twitter::before,
.shariff .shariff-button.twitter .fa-x-twitter::before {
    font-family: "Font Awesome 6 Brands";
    content: "\e61b";
    color: #fff !important;
}
        .zi{
            z-index:1000;
        }
        .zi2{
        overflow:auto;height:auto;
        }
        .bg-button {
            background-color: rgb(37 99 235);
        }
        .bg-button-600{
            background-color: rgb(37 99 235);
        }
        .dark .dark\:hover\:bg-button-700:hover{
            background-color: rgb(17 59 235);
        }
        .pna .bg-button{
                background-color:darkred;

        }
        .pna .bg-button-600{
            background-color: darkred;
        }
        .pna .dark .dark\:hover\:bg-button-700:hover{
            background-color: #DD2222;
        }

        </style>

