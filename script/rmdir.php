<?php
function delDir($dirName) {
   if(empty($dirName)) {
       return;
   }
   if(file_exists($dirName)) {
       $dir = dir($dirName);
       while($file = $dir->read()) {
           if($file != '.' && $file != '..') {
               if(is_dir($dirName.'/'.$file)) {
                   delDir($dirName.'/'.$file);
               } else {
                   @unlink($dirName.'/'.$file) or die('File '.$dirName.'/'.$file.' couldn\'t be deleted!');
               }
           }
       }
       @rmdir($dirName.'/'.$file) or die('Folder '.$dirName.'/'.$file.' couldn\'t be deleted!');
   } else {
       echo 'Folder "<b>'.$dirName.'</b>" doesn\'t exist.';
   }
}
?>