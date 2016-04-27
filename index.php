
<?php 
class HttpClient{
    private $ch;

    function __construct($cookie_jar){
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; Trident/4.0; QQDownload 685; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET4.0C; .NET4.0E)');//UA
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie_jar);
    }

    function __destruct(){
        curl_close($this->ch);
    }

    final public function setReferer($ref=''){
        if($ref != ''){
            curl_setopt($this->ch, CURLOPT_REFERER, $ref);
        }
    }

    final public function Get($url, $header=false, $nobody=false){
        curl_setopt($this->ch, CURLOPT_POST, false);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, $header);
        curl_setopt($this->ch, CURLOPT_NOBODY, $nobody);
        return curl_exec($this->ch);
    }

    final public function Post($url, $data=array(), $header=false, $nobody=false){
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, $header);
        curl_setopt($this->ch, CURLOPT_NOBODY, $nobody);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        return curl_exec($this->ch);
    }
}


const Login_URL = 'http://211.64.47.129/default_ysdx.aspx';

$http = new HttpClient(tempnam('./temp','cookie'));


$html = $http->Get(Login_URL);//先请求登陆页面, 获取 __VIEWSTATE

preg_match('/name="__VIEWSTATE" value="(.+?)"/', $html, $vs);

if(count($vs) !== 2){
    echo '获取viewstate失败';
    exit();
}

//构造登陆时的数据
$data = array(
    '__VIEWSTATE'=>$vs[1],//__VIEWSTATE
    'TextBox1'=>'学号',//修改此处的用户
    'TextBox2'=>'密码',//和密码
    'RadioButtonList1'=>'学生',//以及身份类型
    'Button1'=>'  登录  '
);

$html = $http->Post(Login_URL, $data);

preg_match('/language=\'javascript\'>alert\(\'(.+?)\'\);/', $html, $err);

//检测是否出错, 如果有出错, 则显示错误信息, 然后退出
if(count($err) === 2){
    echo $err[1];
    exit();
}

$sn = '学号';//学号
$name=$http->Get('http://211.64.47.129/xs_main.aspx?xh='.$sn);
if(preg_match_all('/<span[^>]+>(.*)<\/span>/isU', $name, $res)){
	
	$name = substr($res[1][1],14);
	
	$username = substr($name,0,strlen($name)-4); 
	
}else{
	echo "不匹配";
}


$html = $http->Get('http://211.64.47.129/xskbcx.aspx?xh='.$sn.'&xm='.$username.'&gnmkdm=N121603');


if(preg_match_all('/<table[^>]+>(.*)<\/table>/isU', $html, $result)){

	echo "<table  bordercolor='Black' cellpadding='0' cellspacing='0' border='1' width='100%'>";
	print_r($result[1][1]);	

}else{
	echo "不匹配";
}


  
  




 
 ?>
