<?php
class URLShortener
{
    private $urls=[];


    public function generatecode($length =6)
    {
        $characters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code= ' ';

        for($i=0; $i<$length;$i++)
            {
                $code .= $characters[random_int(0,strlen($characters)-1)];
            }

            return $code;
    }

    public function shortenUrl($url)
    {
        do{
             $code= $this->generatecode();
        }while(isset($this->urls[$code]));


        $this->urls[$code]= $url;
        return $code;
    }

    public function getoriginalurl($code)
    {
        return $this->urls[$code] ?? "URL not found";
    }

}

$url= new URLShortener();
$originalurl="https://chatgpt.com/c/6acb200c-4944-83ee-ba19-0efd3334e11b";
$code=$url->shortenUrl($originalurl);

echo "Original URL: ".$originalurl. "\n";
echo "Short Code: ".$code. "\n";
echo "Short URL: http://localhost/". $code. "\n";
echo "Retrieved URL: ".$url->getoriginalurl($code);
?>