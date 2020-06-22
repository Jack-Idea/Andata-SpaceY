require('./bootstrap');

window.Vue = require('vue');

Vue.component('comments-component', require('./components/CommentsComponent.vue').default);

const app = new Vue({
    el: '#app',
});
