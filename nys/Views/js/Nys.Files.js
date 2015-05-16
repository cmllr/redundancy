var nys;
if (typeof nys === "undefined")
    nys = {};

;
(function() {

    /**
    needed jquery extension...
    */
    $.urlParam = function(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        } else {
            return results[1] || 0;
        }
    }
    //https://stackoverflow.com/questions/881510/jquery-sorting-json-by-properties
    function sortJSON(data, key, way) {
        return data.sort(function(a, b) {
            var x = a[key];
            var y = b[key];
            if (way == 1) {
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            }
            if (way == -1) {
                return ((x > y) ? -1 : ((x < y) ? 1 : 0));
            }
        });
    }

    function DisplaySpinner() {
        $(".entry").hide();
        //Hide the spinner on small screens
        $("#spinner").addClass("hidden-xs");
        var opts = {
            lines: 13, // The number of lines to draw
            length: 40, // The length of each line
            width: 10, // The line thickness
            radius: 60, // The radius of the inner circle
            corners: 0.8, // Corner roundness (0..1)
            rotate: 5, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 100, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            // top: '50%', // Top position relative to parent
            //  left: '50%' // Left position relative to parent
        };
        var target = document.getElementById('spinner');
        var spinner = new Spinner(opts).spin(target);
    }

    function HideSpinner() {
        var target = document.getElementById('spinner');

        $(document).ready(function() {
            $("#spinner").html("");
            $(".entry").fadeIn();
        });
    }

    function Init() {
        nys.DisplaySpinner();
        var arguments = [];
        arguments.push(token);

        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'GetFolderList',
            args: arguments
        })
            .done(function(data) {
                targets = $.parseJSON(data);
                $(".entry").remove();
                $("#bc").empty();
                $.contextMenu('destroy');
                var arguments = [];
                arguments.push(currentDir);
                arguments.push(token);
                $.post('./Includes/api.inc.php', {
                    module: 'Kernel.FileSystemKernel',
                    method: 'GetContent',
                    args: arguments
                })
                    .done(function(data) {
                        var files = $.parseJSON(data);
                        if (SortBy == "name") {
                            files = sortJSON(files, "DisplayName", SortOrder);
                        }
                        if (SortBy == "upload") {
                            files = sortJSON(files, "CreateDateTime", SortOrder);
                        }
                        if (SortBy == "size") {
                            files = sortJSON(files, "SizeInBytes", SortOrder);
                        }
                        DisplayContent(files);
                        DisplayBreadcrumbs(currentDir);
                        HideSpinner();                    
                        if (nys.ScrollPosition !== 'undefined')
                           $(window).scrollTop(nys.ScrollPosition);
                    })
                    .fail(function(e) {
                        $("#list").remove();                                    
                        DisplayBreadcrumbs("/");
                        HideSpinner();  
                        currentDir = "/";                      
                        nys.ErrorDialog(e.responseText); 
                    });
            })
            .fail(function(e) {                
                nys.ErrorDialog(e.responseText);
            });
    }

    function DisplayBreadcrumbs(absolutePath) {
        var parts = absolutePath.split("/");
        var completePath = "/";
        $("#bc").append("<li><a href='?files&d=/'>Home</a></li>");
        for (var i = 0; i < parts.length; i++) {
            if (parts[i] != "/" && parts[i] != "" && parts[i] != "/") {
                completePath = completePath + parts[i] + "/";
                $("#bc").append("<li><a href='?files&d=" + completePath + "'>" + parts[i] + "</a></li>");
            }
        }
    }

    function ReturnIcon(mime) {
        var inner = "fa-file-o";
        var files = {};
        if (mime.indexOf("inode") != -1)
            return "<i class=\"fa fa-folder-open-o\"></i>";
        //todo implement a better algorithm
        //add the filetypes
        files["image"] = "fa-image";
        files["zip"] = "fa-compress";
        files["audio"] = "fa-audio-circle";
        files["video"] = "fa-video-camera";
        files["plain"] = "fa-header";
        //office filetypes
        files["msword"] = "fa-keyboard-o";
        files["document"] = "fa-keyboard-o";
        files["presentation"] = "fa-line-chart";
        files["pdf"] = "fa-font";
        files["spreadsheetml"] = "fa-table";
        files["src"] = "fa-code";
        files["ms"] = "fa-windows";
        files["sql"] = "fa-database";
        files["html"] = "fa-html5";
        var length = Object.keys(files).length;
        for (var key in files) {
            var value = files[key];
            if (mime.indexOf(key) != -1) {
                inner = value;
                break;
            }
        }
        var content = "<i class=\"fa " + inner + "\"></i>";
        return content;
    }

    function GetEllipsedDisplayName(name) {
        var parts = name.split(".");
        var displayName = parts[0];
        var extension = parts[1];
        if (displayName.length > 30) {
            var result = "";
            result = displayName.substring(0, 30) + "..." + extension;
            return result;
        } else {
            return name;
        }
    }

    function DisplayContent(data) {      
        for (var i = 0; i < data.length; i++) {
            var content = '';
            content = "<tr class='entry' id='" + data[i].Hash + "'><td>";
            //if (data[i].Thumbnail != true)
            content += ReturnIcon(data[i].MimeType) + "</td>";
            //else
            //	content += "<img src ='"+"./Thumbs/"+data[i].FilePath+"thumb"+"'></td>";
            content += "<td><a title='" + data[i].DisplayName + "' class='filelink' id='HrefOf" + data[i].Id + "' href=''>" + GetEllipsedDisplayName(data[i].DisplayName) + "</a></td><td class='hidden-xs'>" + data[i].CreateDateTime + "</td><td class='size hidden-xs' id='SizeOf" + data[i].Id + "'>" + data[i].SizeWithUnit + "</td>";
            content += "</tr>";
            $('#list').append(content);
            if (data[i].FilePath != null) {
                $("#HrefOf" + data[i].Id).attr("href", "?detail&f=" + data[i].Hash);
                $("#DownloadOf" + data[i].Id).attr("href", "?download&f=" + data[i].Hash);
            } else {
                DisplayLinksForFolder(data[i].Id);
                GetSizeWithUnit(data[i].SizeInBytes, data[i].Id);
            }
            AddContextMenu(data[i]);
        }
    }

    function AppendEntriesToTargetList(entry, currentAbsolutePath) {
        for (var i = 0; i < targets.length; i++) {
            if (currentAbsolutePath != targets[i]) {
                if (targets[i].indexOf(currentAbsolutePath) !== 0) {
                    if (entry.ParentID == "-1") {
                        if (targets[i] != "/") {
                            $(".target").append("<option>" + targets[i] + "</option>");
                        }
                    } else if (targets[i] != currentDir) {
                        $(".target").append("<option>" + targets[i] + "</option>");
                    }
                } else {
                    if (entry.FilePath != null && targets[i] != "/" && currentAbsolutePath != targets[i]) {
                        $(".target").append("<option>" + targets[i] + "</option>");
                    }
                }
            }
        }
    }

    function DisplayMoveOrCopy(entry, move) {
        if (targets != null) {
            MoveOrCopyFileDialog(entry, move, targets);
        }
    }

    function CopyEntry(entry, target) {
        var arguments = [];
        arguments.push(entry.Id);
        arguments.push(target);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'CopyEntryById',
            args: arguments
        })
            .done(function(data) {
                var string = $.parseJSON(data);
                Init();
            })
            .fail(function(e) {
                console.log(e);
                ErrorDialog(e.responseText);
            });
    }

    function MoveEntry(entry, target) {
        //MoveEntry MoveEntryById
        var arguments = [];
        arguments.push(entry.Id);
        arguments.push(target);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'MoveEntryById',
            args: arguments
        })
            .done(function(data) {
                var string = $.parseJSON(data);
                Init();
            })
            .fail(function(e) {
                console.log(e);
                ErrorDialog(e.responseText);
            });
    }

    function RenameEntry(id, newname) {
        var arguments = [];
        arguments.push(id);
        arguments.push(newname);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'RenameEntry',
            args: arguments
        })
            .done(function(data) {
                var res = $.parseJSON(data);
                if (res == false)
                    ErrorDialog("12");
                Init();
            })
            .fail(function(e) {
                console.log(e);
                ErrorDialog(e.responseText);
            });
    }

    function StartDeleteFile(entry) {
        var arguments = [];
        arguments.push(entry.Id);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'GetAbsolutePathById',
            args: arguments
        })
            .done(function(data) {
                var string = $.parseJSON(data);
                Delete(string, false);
            })
            .fail(function(e) {
                nys.ErrorDialog(e.responseText);
            });
    }

    function ErrorDialog(message) {
        var dialogTitle = "";
        var text = "";
        var arguments = [];
        arguments.push("R_ERR_" + message);
        arguments.push(language);
        var regex = /^\d+/;
        if (regex.exec(message) !== null){
            $.post('./Includes/api.inc.php', {
            module: 'Kernel.InterfaceKernel',
            method: 'GetErrorCodeTranslation',
            args: arguments
            })
            .done(function(data) {
                var string = $.parseJSON(data);
                dialogTitle = "Redundancy";
                text = string;
                $("<p>" + text + "</p>").dialog({
                    title: dialogTitle,
                    width: 350,
                    buttons: {
                        "OK": function() {
                            $(this).dialog("close");
                             Init();
                        }
                    }
                });
            });
        }
        else{           
            dialogTitle = "Redundancy";
            text = message;
            $("<p>" + text + "</p>").dialog({
                title: dialogTitle,
                width: 350,
                buttons: {
                    "OK": function() {
                        $(this).dialog("close");
                         Init();
                    }
                }
            });
        }            
    }

    function StartDeleteFolder(entry) {
        var arguments = [];
        arguments.push(entry.Id);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'GetAbsolutePathById',
            args: arguments
        })
        .done(function(data) {
            var string = $.parseJSON(data);
            Delete(string, true);
        })
        .fail(function(e) {
            nys.ErrorDialog(e);
        });
    }

    function CreateDirectory(name) {
        DisplaySpinner();
        var arguments = [];
        arguments.push(name);
        arguments.push(currentDir);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'CreateDirectoryFromCurrentFolder',
            args: arguments
        })
        .done(function(data) {
            console.log(data);
            var string = $.parseJSON(data);
            Init();
        })
        .fail(function(e) {
             nys.ErrorDialog(e.responseText);
        });
    }
    function Delete(path, folder) {
         var arguments = [];
        arguments.push(path);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: (folder) ? 'DeleteDirectory' : 'DeleteFile',
            args: arguments
        })
        .done(function(data) {
            var string = $.parseJSON(data);                
            Init();
        })
        .fail(function(e) {
            nys.ErrorDialog(e.responseText);
        });
    }
    /**
	Display Methods.
  **/
    function DisplayLinksForFolder(id) {
        var arguments = [];
        arguments.push(id);
        arguments.push(token);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'GetAbsolutePathById',
            args: arguments
        })
            .done(function(data) {
                $("#HrefOf" + id).attr("href", "?files&d=" + encodeURI($.parseJSON(data)));
                $("#DownloadOf" + id).attr("href", "?download&d=" + $.parseJSON(data));
            });
    }

    function GetSizeWithUnit(value, id) {
        var arguments = [];
        arguments.push(value);
        $.post('./Includes/api.inc.php', {
            module: 'Kernel.FileSystemKernel',
            method: 'GetCorrectedUnit',
            args: arguments
        })
            .done(function(data) {
                $("#SizeOf" + id).text($.parseJSON(data));
            });
    }

    function GetExistingTargetCount(entry) {
        var currentAbsolutePath = currentDir;
        var existingTargetsCount = 0;
        if (entry.FilePath == null) {
            currentAbsolutePath = currentDir + entry.DisplayName + "/";
            for (var i = 0; i < targets.length; i++) {
                if (currentAbsolutePath != targets[i]) {
                    if (entry.ParentID == "-1") {
                        if (targets[i] != "/") {
                            if (targets[i].indexOf(currentAbsolutePath) !== 0)
                                existingTargetsCount++;
                        }
                    } else if (targets[i] != currentDir) {
                        existingTargetsCount++;
                    }
                }
            }
        } else {
            var currentAbsolutePath = currentDir + entry.DisplayName;
            for (var i = 0; i < targets.length; i++) {
                if (currentAbsolutePath != targets[i]) {
                    if (targets[i].indexOf(currentAbsolutePath) !== 0) {
                        if (entry.ParentID == "-1") {
                            if (targets[i] != "/") {
                                existingTargetsCount++;
                            }
                        } else if (targets[i] != currentDir) {
                            existingTargetsCount++;
                        }
                    } else {
                        if (entry.FilePath != null && targets[i] != "/" && currentAbsolutePath != targets[i]) {
                            existingTargetsCount++;
                        }
                    }
                }
            }
        }
    }

    nys.sortJSON = sortJSON;
    nys.DisplaySpinner = DisplaySpinner;
    nys.HideSpinner = HideSpinner;
    nys.Init = Init;
    nys.DisplayBreadcrumbs = DisplayBreadcrumbs;
    nys.ReturnIcon = ReturnIcon;
    nys.GetEllipsedDisplayName = GetEllipsedDisplayName;
    nys.DisplayContent = DisplayContent;
    nys.AppendEntriesToTargetList = AppendEntriesToTargetList;
    nys.DisplayMoveOrCopy = DisplayMoveOrCopy;
    nys.CopyEntry = CopyEntry;
    nys.MoveEntry = MoveEntry;
    nys.RenameEntry = RenameEntry;
    nys.StartDeleteFile = StartDeleteFile;
    nys.ErrorDialog = ErrorDialog;
    nys.StartDeleteFolder = StartDeleteFolder;
    nys.Delete = Delete;
    nys.DisplayLinksForFolder = DisplayLinksForFolder;
    nys.GetSizeWithUnit = GetSizeWithUnit;
    nys.GetExistingTargetCount = GetExistingTargetCount;
    nys.CreateDirectory = CreateDirectory;
    nys.Source = -1;
    nys.Target = -1;
}());