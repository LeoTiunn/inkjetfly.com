<?php
     
  $originalDirPath = '/';
   $firstLettToMatch = 'a';
   $newDirLetter = 'ssl';
   
   if ($handle = opendir($originalDirPath))
   {   
       while (false !== ($file = readdir($handle)))
       {
           if ($file == '.' || $file == '..') { continue; }
   
           
           {
               copy($originalDirPath.$file, "$originalDirPath/$newDirLetter/$file");
           }
       }
   
       closedir($handle);
   }
     
     ?> 