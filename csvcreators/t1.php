<?php 

$url = 'http://sngapparelinc.com/media/catalog/product/cache/1/thumbnail/320x480/9df78eab33525d08d6e5fb8d27136e95/s/s/ssp8059blk_2.jpg';

echo basename($url);

download_remote_file_with_curl($url,'sng');

function download_remote_file_with_curl($fileUrl,$source)
{
    // Get file name
    $saveTo = basename($fileUrl);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0); 
    curl_setopt($ch,CURLOPT_URL,$fileUrl); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $file_content = curl_exec($ch);
    curl_close($ch);

    if(!file_exists('mport/'.$source.'/')) {
        mkdir ('mport/'.$source.'/',0700, TRUE);
    }

    $downloaded_file = fopen('mport/'.$source.'/'.$saveTo, 'w+');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);

}
?>
