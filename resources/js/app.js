import "./bootstrap.js";
import {createApp} from 'vue'
import FileUpload from "@/components/FileUpload.vue";

const app = createApp({
    components: {
        FileUpload,
    }
});
app.mount('#app');

//
// const app = new Vue({
//     el: '#app',
// });
