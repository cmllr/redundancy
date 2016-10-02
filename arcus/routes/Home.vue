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
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Last modified</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="dir !== '/'">                            
                        <td>
                            <a class="parent-dir" v-on:click="open(dirData.ParentID,true)">
                                ../    
                            </a>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr v-for="item in dirContents">                            
                        <td> 
                           <i class="fa" v-bind:class="{'fa-folder': item.FilePath === null,'fa-file-o': item.FilePath !== null  }" ></i>
                  
                            <a  v-on:click="open(item.Id,item.FilePath == null)" >                       
                                    {{ item.DisplayName }} 
                            <a>
                        </td>
                        <td>{{ item.LastChangeDateTime | date }}</td>
                        <td>{{ item.SizeWithUnit }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
         <div class="modal fade" id="file-modal" v-if="file !== null">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title modal-file-name">{{ file.DisplayName }}</h4>
            </div>
            <div class="modal-body">
                <p v-if="file !== null && file.MimeType.indexOf('image') !== -1">
                    <img class="image-preview" :src="'./image.php?i='+file.FilePath"></img>
                </p>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="list-group-item-heading">Size</h5>
                        <p class="list-group-item-text">{{ file.SizeWithUnit }}</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="list-group-item-heading">Type</h5>
                        <p class="list-group-item-text">{{ file.MimeType }}</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="list-group-item-heading">Created</h5>
                        <p class="list-group-item-text"> {{ file.CreateDateTime | date }}</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <h5 class="list-group-item-heading">Changed</h5>
                        <p class="list-group-item-text"> {{ file.LastChangeDateTime | date }}</p>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->   
    </div>
        
</script>

<script type="text/javascript">
$('#myModal').modal();
const Home = {
  template: '#home', 
  replace: true,
  data: function(){
      return {
          user: {},
          dir: '/',
          dirContents: [],
          dirData: null,
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
        var args = JSON.stringify([id,this.$parent.token]);
        this.$http.post(API,{method:'GetEntryById',module:'Kernel.FileSystemKernel',args:args}).then((response) => {  
            this.file = JSON.parse(response.data);
            $('#file-modal').modal('show');
        }, (response) => {
            console.log(response);
        });  
     },
     show:function(path){     
        localStorage.setItem("dir",path);       
        this.dir = localStorage.getItem("dir");
        var args = JSON.stringify([path,this.$parent.token]);
        this.$http.post(API,{method:'GetEntryByAbsolutePath',module:'Kernel.FileSystemKernel',args:args}).then((response) => {  
            this.dirData = JSON.parse(response.data);   
        }, (response) => {
            console.log(response);
        }); 
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
  }
};
</script>