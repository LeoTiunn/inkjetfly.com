<?php
mkdir('/ssl/admin');
mkdir('/ssl/includes');
mkdir('/ssl/templates');
mkdir('/ssl/images');

require('/script/copydirr.inc.php');
copydirr('/admin','/ssl/admin',0777,true);
copydirr('/includes','/ssl/includes',0777,true);
copydirr('/templates','/ssl/templates',0777,true);
copydirr('/images','/ssl/images',0777,true);

chmod("/includes/configure.php", 0444);
chmod("/images", 0777);
chmod("/admin/includes/configure.php", 0644);
chmod("/admin/images/graphs", 0777);
chmod("/admin/backups", 0777);
chmod("/ssl/includes/configure.php", 0444);
chmod("/ssl/images", 0777);
chmod("/ssl/admin/includes/configure.php", 0644);
chmod("/ssl/admin/images/graphs", 0777);
chmod("/ssl/admin/backups", 0777);
?>