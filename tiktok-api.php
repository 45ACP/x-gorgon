<?php


class TiktokApi {

  public $api_url;


  public $cookies_dir;
  public $setUser;
  public $useragent = 'com.zhiliaoapp.musically.go/382 (Linux; U; Android 5.1; ru_RU; ZTE T630; Build/LMY47D; Cronet/TTNetVersion:4df3ca9d 2021-08-08)';

  public function __construct($settings = []) {
    $this->api_url = 'https://api2-19-h2.musical.ly/';
    $this->cookies_dir = __DIR__;
  }

  public function setCookieDir($dir) {
      $this->cookies_dir = $dir.'/';
  }

  public static function xorEncrypt(
      $data,
      $key = 5) {
      $xored = '';
      for ($i = 0; $i < strlen($data); ++$i) {
          $xored .= bin2hex(chr(ord($data[$i]) ^ $key));
      }

      return $xored;
  }

  public function login($username,$password,$captcha = null) {
      $this->setUser = $this->cookies_dir.$username.'-cookie.txt';
      return $this->request('passport/user/login/?',array(
          'mix_mode'  => 1,
          'username'  => $this -> xorEncrypt($username),
          'email'     => $this -> xorEncrypt($username),
          'mobile'    => '',
          'account'   => '',
          'password'  => $this -> xorEncrypt($password),
          'captcha'   => $captcha
      ));
  }

  public function sendRequestLogin($username, $password, $captcha = null) {

    $url = "https://" . $this -> api_url . "passport/user/login/";

    $curl = curl_init($url);

    if (!$curl) {
      echo 'Unable to initialize curl.';
    }

    // $getFileList = $this -> getFileList();
    //
    // curl_setopt($curl, CURLOPT_PROXY, $getFileList['proxy']);
    // curl_setopt($curl, CURLOPT_PROXYUSERPWD, $getFileList['auth']);

    curl_setopt($curl, CURLOPT_FAILONERROR,    false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,        15);

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'User-Agent: ' . $this -> useragent,
      'X-Gorgon: 040100d50000da259fb9bbbce2453d0d129bf3a633ee1652eba1',
      'X-Khronos: 1588879554',
      'x-tt-trace-id: 00-f0984bc8105eb44ddcb24bc6056d053c-f0984bc8105eb44d-01',
      'X-Tt-Token: 0379c97d64a0c7a25e165dbf2b94ad2c47cfe5b43723711294bdac1460512dabeea9f3eaf1547e6ef933d8b228cd9b619c27',
      'Host: ' . $this -> api_url
    ));

    curl_setopt($curl, CURLOPT_USERAGENT, $this -> useragent);

    // $cookies = 'act=f432ddcdebc049c7b40b3c2cdc427520; mrcu=BD81610A57B9253030DED970F1C0';
    // curl_setopt($curl, CURLOPT_COOKIE, "act={$this -> cookies_act}; mrcu={$this -> $cookies_mrcu}");

    // Create And Save Cookies
    // $tmpfname = dirname(__FILE__) . '/cookie.txt';
    // curl_setopt($curl, CURLOPT_COOKIEJAR, $tmpfname);
    // curl_setopt($curl, CURLOPT_COOKIEFILE, $tmpfname);

    $data = array(
      'mix_mode'  => 1,
      'username'  => $this -> xorEncrypt($username),
      'email'     => $this -> xorEncrypt($username),
      'mobile'    => '',
      'account'   => '',
      'password'  => $this -> xorEncrypt($password),
      'captcha'   => $captcha
    );

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($curl);

    $response = json_decode($response);

    curl_close($curl);

		print_r($response);

  }


  public function SetUser($username) {
      return $this->setUser = $this->cookies_dir.$username.'-cookie.txt';
  }

  public function userInfo($user_id) {
      return $this->request('aweme/v1/user/?user_id='.$user_id.'&'.$this->requestArray());
  }

  public function userMedias($user_id,$max_cursor = 0){
      return $this->request('aweme/v1/aweme/post/?max_cursor='.$max_cursor.'&user_id='.$user_id.'&count=10&retry_type=no_retry&'.$this->requestArray());
  }

  public function userFollowers($user_id,$max_time = null){
      if($max_time == null){ $max_time = (time() *1000); }
      return $this->request('aweme/v1/user/follower/list/?user_id='.$user_id.'&count=10&max_time='.$max_time.'&retry_type=no_retry&'.$this->requestArray());
  }

  public function userFollowing($user_id,$max_time = null){
      if($max_time == null){ $max_time = (time() *1000); }
      return $this->request('aweme/v1/user/following/list/?user_id='.$user_id.'&count=10&max_time='.$max_time.'&retry_type=no_retry&'.$this->requestArray());
  }

  public function getVideoDetail($video_id){
      return $this->request('aweme/v1/aweme/detail/?aweme_id='.$video_id.'&'.$this->requestArray());
  }

  public function getComments($video_id,$cursor = 0){
      return $this->request('aweme/v1/comment/list/?aweme_id='.$video_id.'&comment_style=2&digged_cid&insert_cids&?count=100&cursor='.$cursor.'&'.$this->requestArray());
  }

  public function follow($id){
      return $this->request('aweme/v1/commit/follow/user/?user_id='.$id.'&type=1&retry_type=no_retry&from=3&'.$this->requestArray());
  }

  public function Verify(){
      return $this->outRequest('https://verification-va.byteoversea.com/get?'.$this->requestArray());
  }

  public function getUserFollowers(){

  }

  public function PopularCategory(){
      return $this->request('aweme/v1/category/list/?'.$this->requestArray());
  }

  public function ForYou(){
      return $this->request('aweme/v1/feed/?count=25&offset=0&max_cursor=0&min_cursor=0&type=0&is_cold_start=1&pull_type=0&req_from&'.$this->requestArray());
  }

  public function searchUser($username){
      return $this->request('aweme/v1/discover/search/?cursor=0&keyword='.$username.'&count=10&type=1&hot_search=0&'.$this->requestArray());
  }

  public function searchHashtag($hashtag){
      return $this->request('aweme/v1/challenge/search/?cursor=0&keyword='.$hashtag.'&count=10&type=1&hot_search=0&'.$this->requestArray());
  }

  public function listHashtag($hashtagID,$cursor = 0){
      return $this->request('aweme/v1/challenge/aweme/?ch_id='.$hashtagID.'&count=20&offset=0&max_cursor=0&type=5&query_type=0&is_cold_start=1&pull_type=1&cursor='.$cursor.'&'.$this->requestArray());
  }

  public function headers(){
    return array(
      'Host' => $this->api_url,
      'x-ss-ts' => "0",
      'User-Agent' => "com.zhiliaoapp.musically/2019091803 (Linux; U; Android 9; en_GB; FIG-LX1; Build/HUAWEIFIG-L31; Cronet/58.0.2991.0)",
      'accept-encoding' => "gzip",
      'connection' => "keep-alive",
      'x-tt-token' => "039a9b40fa2af592979397f52d3b2e77eb63f401c58c982223732eb970a0f8d2c364b9bec911bccb27372e9df5591b63a75d",
      'X-SS-QUERIES' => 'dGMCD76ot3awANG2fsgrAefIrbjUj0h4sPe6DvxFZh180nmH2rdzDJP0tBZ%2BcDUBfA%2FV4doKvJf4VawAAOu%2ByhyWDII%2BhuzsnqwgLlCM%2FjNc8fV7',
      'sdk-version' => "1",
      'x-ss-dp' => "1340",
      'x-tt-trace-id' => "00-da992fbd105e4fc5d34d864605f6053c-da992fbd105e4fc5-01"
      // 'x-khronos' => '1583430886',
      // 'x-gorgon' => '83005ff00000e4f54053e523d1c4a9b9ccf1a2175a263e6cb706'
    );
  }

  public function request($endpoint,$post = null){


      $curl = curl_init();

      $getFileList = $this -> getFileList();

      curl_setopt($curl, CURLOPT_PROXY, $getFileList['proxy']);
      curl_setopt($curl, CURLOPT_PROXYUSERPWD, $getFileList['auth']);

      $options = array(
          CURLOPT_URL => $this->api_url.$endpoint,
          CURLOPT_USERAGENT => 'com.zhiliaoapp.musically.go/312 (Linux; U; Android 9; ru_RU; SM-A105F; Build/PPR1.180610.011; Cronet/TTNetVersion:4df3ca9d 2021-08-08)',
          CURLOPT_REFERER => $this->api_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_COOKIEFILE => $this->setUser,
          CURLOPT_COOKIEJAR => $this->setUser,
          CURLOPT_HTTPHEADER => $this->headers()
      );
      if($post){
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = $post;
      }
      curl_setopt_array($curl,$options);
      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response);
  }

  public function outRequest($page,$post = null){
      $curl = curl_init();
      $options = array(
          CURLOPT_URL => $page,
          CURLOPT_USERAGENT => 'com.zhiliaoapp.musically.go/312 (Linux; U; Android 9; ru_RU; SM-A105F; Build/PPR1.180610.011; Cronet/TTNetVersion:4df3ca9d 2021-08-08)',
          CURLOPT_REFERER => $this->api_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_COOKIEFILE => $this->setUser,
          CURLOPT_COOKIEJAR => $this->setUser,
          CURLOPT_HTTPHEADER => $this->headers()
      );
      if($post){
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = $post;
      }
      curl_setopt_array($curl,$options);
      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response);
  }

  public function requestArray(){
      $items = array(
          'pass-region'           => '1',
          'pass-route'            => '1',
          'app_type'              => 'normal',
          'os_api'                => '28',
          'device_type'           => 'SM-A105F',
          'ssmix'                 => 'a',
          'manifest_version_code' => '312',
          'dpi'                   => '280',
          'carrier_region'        => 'RU',
          'app_name'              => 'musically_go',
          'version_name'          => '3.1.2',
          'timezone_offset'       => '10800',
          'pass-region'           => '1',
          'is_my_cn'              => 0,
          'ac'                    => 'wifi',
          'update_version_code'   => '312',
          'channel'               => 'googleplay',
          '_rticket'               => time(),
          'device_platform'       => 'android',
          'iid'                   => '6644690260088899334',
          'build_number'          => '3.1.2',
          'version_code'          => '990',
          'timezone_name'         => 'Europe/Moscow',
          'openudid'              => '4873ead325c1d438',
          'device_id'             => '6640961254070470149',
          'sys_region'            => 'RU',
          'app_language'          => 'ru',
          'resolution'            => '720*1382',
          'os_version'            => '9',
          'device_brand'          => 'samsung',
          'language'              => 'ru',
          'aid'                   => '1340',
          'mcc_mnc'               => '25099',
          'as'                    => 'a185f153a9cf3cf2790022',
          'cp'                    => '10f8ce5c9b963b20e1_mMq',
          'mas'                   => '016f24b4e638ac381d803002da9b8cdfc64c4c0c0c9cec4c66c6cc'
      );

      foreach ($items as $key => $item){
          $packet[] = $key.'='.$item;
      }
      $implode = implode('&',$packet);
      return $implode;
  }

  public function getFileList() {

    $string = file_get_contents('proxy_list.json');
    $json_a = json_decode($string, true);

    $rand_keys = array_rand($json_a['results'], 1);

    $result = $json_a['results'][$rand_keys];

    $auth = $result['username'] . ':' . $result['password'];
    $proxy = $result['proxy_address'] . ':' . $result['ports']['http'];

    $res = array('auth' => $auth, 'proxy' => $proxy);

    return $res;

    // print_r($res);

    // print_r($rand_keys);

  }


}



?>
