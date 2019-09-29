/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('admin-lte');


//my vue code
import Vue from 'vue'
import VueRouter from 'vue-router'
import {Form, HasError, AlertError} from 'vform'
import moment from 'moment';
import VueProgressBar from 'vue-progressbar'
import Swal from 'sweetalert2'

import Gate from './Gate'

Vue.prototype.$gate = new Gate(window.user); //to use it on all the application
// if the current user is authunticated i will send u an http request and store it in the window


window.Swal = Swal;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
})
window.Toast = Toast;


Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
})


window.Vue = require('vue');
window.Form = Form; //we register it globally anyone can use it

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)


Vue.use(VueRouter)

let routes = [
    {path: '/dashboard', component: require('./components/Dashboard.vue').default},
    {path: '/profile', component: require('./components/Profile.vue').default},
    {path: '/developer', component: require('./components/Developer.vue').default},
    {path: '/users', component: require('./components/Users.vue').default},
    {path: '/*', component: require('./components/NotFound.vue').default} //if the path does not  exist
]


const router = new VueRouter({
    // i take the name of color class fron the _variables.scss that i created over there
    linkActiveClass: 'text-white my_red', //instead of writing on each router link => active-class="font-bold"

    mode: 'history',
    routes
})

// registering the filters globally
Vue.filter('upText', function (text) {
    return text.charAt(0).toUpperCase() + text.slice(1)
});

Vue.filter('myDate', function (created) {
    return moment(created).format('MMMM Do YYYY');
});


window.Fire = new Vue(); //for events

//laravel passport
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

/////////
Vue.component(
    'not-found',
    require('./components/NotFound.vue').default
);

/////////
Vue.component('pagination', require('laravel-vue-pagination'));


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data: {
        search: ''
    },
    methods: {
        searchit: _.debounce(() => {// wait for seconds and then call function

            Fire.$emit('searching'); // create an event called searching
            // console.log('WELL !!')
        }, 2000),

        printme() {
            window.print();
        }


    }

});
