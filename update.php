<?php
header("Content-Type: text/plain; charset=UTF-8");
function curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
        $shasum = curl('https://raw.githubusercontent.com/iLay1678/wechat_for_handsome/master/shasum.txt');

        echo "获取最新特征库...\n";
        echo $shasum."\n\n";

        $shasum = explode("\n", $shasum);
        array_pop($shasum);

        echo "开始检查本地文件...\n";

        foreach ($shasum as $remote) {
            list($remote_sha256, $filename) = explode('  ', $remote);
            if (!file_exists(__DIR__.'/'.$filename) ||
                !hash_equals(hash('sha256', file_get_contents(__DIR__.'/'.$filename)), $remote_sha256)) {
                echo "下载     ".$filename;
                $url = 'https://raw.githubusercontent.com/iLay1678/wechat_for_handsome/master/'.substr($filename, 1);

                if (file_put_contents(__DIR__.'/'.$filename, curl($url))) {
                    echo " (OK)\n";
                } else {
                    die("\n下载失败，错误信息: $url\n");
                }
            } else {
                echo "无需更新  ".$filename."\n";
            }
        }
echo "\n\n更新完成。";
        die();
