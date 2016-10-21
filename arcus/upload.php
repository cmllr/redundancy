<?php
$file = $_FILES['file'];
//tmp files will be deleted after script execution -> move it to a new name
$oldPath = $file['tmp_name'];
move_uploaded_file($oldPath,$oldPath.'REDUNDANCY');
$file['tmp_name'] = $oldPath.'REDUNDANCY';
$got =  DoRequest('https://r2.0fury.de/Includes/api.inc.php','Kernel.FileSystemKernel','UploadFileWrapper',json_encode(array($_GET['dir'],$_GET['token'],json_encode($file))));	
echo $got;
function DoRequest($api,$module,$method,$args){
        $postdata = http_build_query(
            array(
                'module' => $module,
                'method' => $method,
                'args' => $args,
                'ip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'ignore_errors' => true,
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata,
                'user_agent' => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : 'Nys'
            )
        );
        $context  = stream_context_create($opts);
        $resp = file_get_contents($api, false, $context);
        if (is_int(json_decode($resp))){
            error_log($resp);
            header('HTTP/1.1 403 Forbidden');
            //Special handling if the file upload is used.
            if ($method=='UploadFileWrapper'){
                header('Content-type: text/plain');
                exit('##R_ERR_'.$resp);
            }
        }
        return json_decode($resp);
}
