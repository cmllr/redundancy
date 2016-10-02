<html>
  <head>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/css/tether-theme-basic.min.css" />
     <link rel="stylesheet" href="./arcus.css">
  </head>
  <body>
    <div id="app" class="container">      
      <router-view></router-view>
    </div>
  </body>
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-router/2.0.0/vue-router.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.0.3/vue-resource.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.slim.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>

<script>
Vue.http.options.emulateJSON = true;
Vue.debug = true;
</script>
<?php include "./routes/LogIn.vue";?> 
<?php include "./routes/Home.vue";?> 
<script>
const API = "http://web/redundancy/Includes/api.inc.php";
const router = new VueRouter({
  root: '/',
  routes:[
    { path: '/home/', component: Home},
    { path: '/', component: LogIn}
  ]
});
Vue.use(VueRouter)
const app = new Vue({
  router,
  el: "#app",
  data: {
    title: "Arcus",
    token: ""
  },
  created: function(){
     document.title = this.title;
  },
  ready:function(){

  },
  computed: {
    
  },
  methods: {
    
  }
});
Vue.filter('date', function (value) {
  //2. Oct 2016 - 12:40
  return moment(value, "D. MMM - H:mm").fromNow();
})
</script>
