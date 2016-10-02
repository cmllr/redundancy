<script type="text/x-template" id="login">    
    <div class="row">
        <div class="col-lg-4">
        </div>
        <div class="col-lg-4">
            <div class="alert alert-danger" v-if="authError">
                Authentification error!
            </div>
            <form>
                <div class="form-group">
                    <label for="formGroupExampleInput">Username</label>
                    <input type="text" class="form-control" v-model="username">
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput2">Password</label>
                    <input type="password" class="form-control" v-model="password" >
                </div>
                 <div class="form-group">
                    <button class="btn btn-default" v-on:click="LogIn">LogIn</button>
                </div>
            </form>
        </div>       
        <div class="col-lg-4">
        </div>
    </div>    
</script>

<script type="text/javascript">
const LogIn = {
  template: '#login', 
  replace: true,
  data: function(){
      return {
            username: 'foo',
            password: 'bar',
            stayLoggedIn: false,
            authError: false
      };
  },
  ready:function(){
      if (localStorage.getItem("token") !== null){
           window.location.href="#/home"; 
      }
  },
  methods:{
      LogIn:function(){
        var data = [this.username,this.password,this.stayLoggedIn]
        var args = JSON.stringify(data);
        this.$http.post(API,{method:'LogIn',module:'Kernel.UserKernel',args:args}).then((response) => {
            this.authError = false;
            app.token = JSON.parse(response.data);
            localStorage.setItem('token', app.token);    
            window.location.href="#/home"; 
        }, (response) => {
            this.authError = true;
        });
      }
  }
};
</script>