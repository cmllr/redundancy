<html>
  <head>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/css/bootstrap.min.css">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/js/bootstrap.min.js"></script>



<script>
Vue.http.options.emulateJSON = true;
Vue.debug = true;
</script>
<?php include "./routes/Home.vue";?> 
<script>
const API = "http://web/redundancy/Includes/api.inc.php";
const router = new VueRouter({
  root: '/',
  routes:[
    { path: '/', component: Home}
  ]
});
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
</script>
