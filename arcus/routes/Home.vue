<script type="text/x-template" id="home">
    <div>
      <div class="row">
        <div class="col-lg-10">
          <img class="header-logo" src="./logo.png">
          <span class="header-middle">
            <span class="header-vendor">
              {{ this.$parent.title }}
            </span>
            <span class="header-instance">
              {{ this.$parent.instance }}
            </span>
            <span class="header-dir">
              {{ this.dir }}
            </span>
          </span>
        </div>
      </div>
      <div class="row">
            	<div class="col-md-3">
			<div class="profile-sidebar">
                <div class="profile-userpic">
					<img :src="user.Gravatar" class="img-responsive" alt="">
				</div>
				<div class="profile-usertitle">
					<div class="profile-usertitle-name">
						{{ user.DisplayName }}
					</div>
					<div class="profile-usertitle-job">
						@{{ user.LoginName }}
					</div>
				</div>
				<div class="profile-usermenu">
					<ul class="nav">
						<li>
							<a href="#/home">
							<i class="glyphicon glyphicon-home"></i>
							Home </a>
						</li>
						<li>
							<a href="#">
							<i class="glyphicon glyphicon-user"></i>
							Account Settings </a>
						</li>
                        <li>
							<a href="#" v-on:click="browseFile">
							<i class="glyphicon glyphicon-upload"></i>
							Upload </a>
						</li>
                        <li>
							<a href="#" v-on:click="newFolder" data-toggle="modal" data-target=".bs-example-modal-sm">
							<i class="glyphicon glyphicon-folder-open"></i>
							New folder </a>
              <input v-if="folder != null" type="text" class="form-control" v-model="folder" v-on:keyup.enter="createFolder">
						</li>
                        <li>
							<a href="#/logout">
							<i class="glyphicon glyphicon-log-out"></i>
							Logout </a>
						</li>
					</ul>
				</div>
				<!-- END MENU -->
			</div>
		</div>
          <div class="col-lg-9">
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
                              <a  href="#"  class="parent-dir" v-on:click="open($event,dirData.ParentID,true)">
                                  ../
                              </a>
                          </td>
                          <td></td>
                          <td></td>
                      </tr>
                      <tr v-for="item in dirContents">
                          <td>
                             <i class="fa" v-bind:class="{'fa-folder': item.FilePath === null,'fa-file-o': item.FilePath !== null  }" ></i>

                              <a href="#" v-on:click="open($event,item.Id,item.FilePath == null)" >
                                      {{ item.DisplayName }}
                              <a>
                          </td>
                          <td>{{ item.LastChangeDateTime | date }}</td>
                          <td>{{ item.SizeWithUnit }}</td>
                      </tr>
                  </tbody>
              </table>
              <input class="hidden" type="file" id="file-upload" v-on:change="upload">
          </div>
          <div v-if="file !== null" id="demoLightbox" class="lightbox hide fade"  tabindex="-1" role="dialog" aria-hidden="true">
            <div class='lightbox-content'>
              <img :src="'./image.php?i='+file.FilePath">
              <div class="lightbox-caption"><p>Your caption here</p></div>
            </div>
          </div>
           <div class="modal fade " data-backdrop="false" id="file-modal" v-if="file !== null">
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
          file:null,
          upload:null,
          folder:null
      };
  },
  methods:{
     getUser:function(){
        var args = JSON.stringify([this.$parent.token]);
        this.$http.post(API,{method:'GetUser',module:'Kernel.UserKernel',args:args}).then((response) => {
           var result = JSON.parse(response.data);
           this.user = result;
           this.user.Gravatar = "https://www.gravatar.com/avatar/" + CryptoJS.MD5(this.user.MailAddress);
        }, (response) => {
          console.log(responsglyphicone);
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
     open:function($event,id,isDir){
        $event.preventDefault();
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
     },
     browseFile: function(e){
         e.preventDefault();
         document.getElementById('file-upload').click();
     },
     upload: function(e) {
        e.preventDefault();
        var files = e.target.files;
        var formData = new FormData();
        formData.append('file', files[0]);
        connect = new XMLHttpRequest();
        var vue = this;
        connect.onreadystatechange = function (e) {
            if(connect.readyState == 4 && connect.status == 200) {
                 vue.getFiles();
            }
        };
        connect.onprogress = function(e){
            if (e.lengthComputable) {
                var percentComplete = e.loaded / e.total;
                console.log(percentComplete);
            } else {
                // Unable to compute progress information since the total size is unknown
            }
        }
        connect.onerror = function(e){
            console.log(e);
        }

        var args = JSON.stringify([this.dirData.Id,this.$parent.token,JSON.stringify(formData)]);
        //upload throught wrapper
        connect.open('POST', "./upload.php?dir="+this.dirData.Id+"&token="+this.$parent.token,true);
        connect.send(formData);
    },
    newFolder: function(e){
        e.preventDefault();
        if (this.folder === ""){
          this.folder = null;
        }else{
          this.folder = "";
        }
    },
    createFolder: function(){
      console.log(this.folder);
      //CreateDirectory($name,$folder->Id,$token)
      var args = JSON.stringify([this.folder,this.dirData.Id,this.$parent.token]);
      this.$http.post(API,{method:'CreateDirectory',module:'Kernel.FileSystemKernel',args:args}).then((response) => {
        console.log(response);
        this.getFiles();
        this.folder = null;
      }, (response) => {
          console.log(response);
      });
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
