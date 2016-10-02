<script type="text/x-template" id="home">
    <div class="row">
        <div class="col-lg-2">
        <div class="dropdown closed">
            <a class="btn btn-secondary dropdown-toggle" href="http://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ user.DisplayName }}
            </a>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="#/logout">Logout</a>
            </div>
        </div>      
        </div>
        <div class="col-lg-10">
        {{ $route.params.path }}
            <div class="list-group">
                <a class="list-group-item active">
                    <a class="crumb" href="#" v-for="crumb in breadcrumbs">
                        {{ crumb }}
                    </a>
                </a>
                <a v-for="item in dirContents"  class="list-group-item list-group-item-action">
                     <i class="fa" v-bind:class="{'fa-folder': item.FilePath === null,'fa-file-o': item.FilePath !== null  }" ></i>
                  
                    <a  v-on:click="open(item.Id,item.FilePath == null)">                       
                        {{ item.DisplayName }} 
                    <a>
                    <span class="file-meta">
                        {{ item.SizeWithUnit }}
                    </span>
                    <span class="file-meta">
                        {{ item.CreateDateTime | date }}
                    </span>
                </a>
            </div>
        </div>
         <div class="modal fade" id="file-modal" v-if="file !== null">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{{ file.DisplayName }}</h4>
            </div>
            <div class="modal-body">
                <p>
                    <img v-if ="file !== null && file.MimeType.indexOf('image') !== -1" v-attr="src: './image.php?i='file.FilePath'"></img>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->   
    </div>
        
</script>

<script type="text/javascript">
const Home = {
  template: '#home', 
  replace: true,
  data: function(){
      return {
          user: {},
          dir: '/',
          dirContents: [],
          file:null
      };
  },
  methods:{
     getUser:function(){
        var args = JSON.stringify([this.$parent.token]);
        this.$http.post(API,{method:'GetUser',module:'Kernel.UserKernel',args:args}).then((response) => {
           var result = JSON.parse(response.data);
           this.user = result;
        }, (response) => {
          console.log(response);
        });
     },
     getFiles:function(){
        var args = JSON.stringify([this.dir,this.$parent.token]);
        this.dirContents = [];  
        this.$http.post(API,{method:'GetContent',module:'Kernel.FileSystemKernel',args:args}).then((response) => {
           this.dirContents = JSON.parse(response.data);
        }, (response) => {
          console.log(response);
        });
     },
     open:function(id,isDir){        
        if (!isDir){
            this.openFile(id);
            return;
        }
        var args = JSON.stringify([id,this.$parent.token]);
        this.$http.post(API,{method:'GetAbsolutePathById',module:'Kernel.FileSystemKernel',args:args}).then((response) => {  
            this.show(JSON.parse(response.data));
        }, (response) => {
          console.log(response);
        });       
     },
     openFile:function(id){
         console.log(id);
         //GetEntryById
        var args = JSON.stringify([id,this.$parent.token]);
        this.$http.post(API,{method:'GetEntryById',module:'Kernel.FileSystemKernel',args:args}).then((response) => {  
            this.file = JSON.parse(response.data);
            $('#file-modal').modal();
            console.log(this.file);
        }, (response) => {
        console.log(response);
        });  
     },
     show:function(path){     
        localStorage.setItem("dir",path);       
        this.dir = localStorage.getItem("dir");
        this.getFiles();       
     }
  },
  created:function(){
      if (localStorage.getItem("token") === null){
          //redirect to login
          window.location.href="#/"; 
      }else{            
        this.$parent.token = localStorage.getItem("token");
        var storageDir = localStorage.getItem("dir"); 
        this.dir =  storageDir !== null ? storageDir : "/";
        this.show(this.dir);
        this.getUser();
      }
  },
  computed:{
      breadcrumbs:function(){
          var parts = this.dir.split('/');
          parts.unshift("/");
          return parts;
      }
  }
};
</script>