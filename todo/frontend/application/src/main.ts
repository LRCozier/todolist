import { createApp } from 'vue'
import './style.css'
import App from './App.vue'

import { library } from '@fortawesome/fontawesome-svg-core';
import { faTrash } from '@fortawesome/pro-light-svg-icons';
import { faPencil } from '@fortawesome/pro-light-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faTrash);
library.add(faPencil);

createApp(App).mount('#app')
app.component('font-awesome-icon', FontAwesomeIcon);
app.mount('#app');
