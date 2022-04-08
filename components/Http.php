<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;

/**
 * Desc 网络访问基础组件
 * Class Http
 * @package app\components
 * @author shiqingsheng
 *
 * 1.支持5种Http请求：GET、POST、PUT、DELETE、HEAD
 * 2.支持设置Cookie、UA
 * 3.支持设置Request Header
 * 4.支持文件上传
 * 5.支持设置代理：HTTP、SOCKS5
 * 6.支持设置证书：PEM、DER、ENG
 * 7.支持Response Header、Response Body、Response Info、Response Error
 */
class Http extends Component
{
    /**
     * 超时时间
     * @var int
     */
    private $_iTimeOut = 30;

    /**
     * 毫秒超时时间
     * @var int
     */
    private $_iMsTimeOut = 0;

    /**
     * 连接超时时间
     * @var int
     */
    private $_iConnectTimeOut = 10;

    /**
     * 错误信息
     * @var int
     */
    private $_sErrorInfo = '';

    /**
     * 返回信息
     * @var array
     */
    private $_aReturnInfo = array();

    /**
     * request header
     * @var array
     */
    private $_aHeader = array();

    /**
     * response header
     * @var string
     */
    private $_sResponseHeader = '';

    /**
     * 请求cookie
     * @var string
     */
    private $_sCookie = '';

    /**
     * 上传文件地址
     * @var string
     */
    private $_aUploadFile = array();

    /**
     *
     * @var string
     */
    private $_sBody = '';

    /**
     * UA
     * @var string
     */
    private $_sUserAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)";

    /**
     * 代理
     * @var null
     */
    private $_sProxyHost = NULL;
    private $_sProxyPort = NULL;
    private $_sProxyUser = NULL;
    private $_sProxyPwd  = NULL;
    private $_sProxyType = NULL;//CURLPROXY_HTTP、CURLPROXY_SOCKS4、CURLPROXY_SOCKS5

    /**
     * 证书类型，PEM、DER、ENG
     * @var string
     */
    private $_sSSLCertType = 'PEM';

    /**
     * 证书存放路径
     * @var string
     */
    private $_sSSLCertFile = '';

    /**
     * 证书密码
     * @var string
     */
    private $_sSSLCertPwd  = '';

    /**
     * 私钥类型，PEM、DER、ENG
     * @var string
     */
    private $_sSSLKeyType = 'PEM';

    /**
     * 私钥存放路径
     * @var string
     */
    private $_sSSLKeyFile  = '';

    public function __construct()
    {
        if(!function_exists('curl_init'))
        {
            throw new Exception('ErrorCurlNotSupported');
        }
    }

    /**
     * 设置超时时间(单位秒)
     * @param $iTimeOut
     */
    public function setTimeOut($iTimeOut)
    {
        $this->_iTimeOut = $iTimeOut;
    }

    /**
     * 设置毫秒超时(单位毫秒)
     * @param $iMsTimeOut
     */
    public function setMsTimeOut($iMsTimeOut)
    {
        $this->_iMsTimeOut = $iMsTimeOut;
    }

    /**
     * 设置连接超时时间
     * @param $iTimeOut
     */
    public function setConnectTimeOut($iTimeOut)
    {
        $this->_iConnectTimeOut = $iTimeOut;
    }

    /**
     * 设置request header
     * @param $aHeader
     */
    public function setHeader($aHeader){
        $this->_aHeader = $aHeader;
    }

    /**
     * 设置request UserAgent
     * @param $sUa
     */
    public function setUserAgent($sUa)
    {
        $this->_sUserAgent = $sUa;
    }

    /**
     * 设置request cookie
     * @param $aCookie
     */
    public function setCookies($aCookie)
    {
        foreach($aCookie as $sName => $sValue)
        {
            $this->_sCookie .= $sName."=".$sValue.'; ';
        }
    }

    /**
     * 设置单个上传文件
     * @param $sFileParam
     * @param $sFilePath
     * @throws Exception
     */
    public function setUploadFile($sFileParam,$sFilePath){
        if(!is_readable($sFilePath)){
            throw new Exception($sFilePath.' Not Readable');
        }

        $this->_aUploadFile = array(
            "$sFileParam"=>"@$sFilePath",
            "upload"=>"Upload",
        );
    }

    /**
     * 设置消息体内容
     * @param $sBody
     */
    public function setBody($sBody){
        $this->_sBody = $sBody;
    }

    /**
     * 取response header
     * @return string
     */
    public function getResponseHeader(){
        return $this->_sResponseHeader;
    }

    /**
     * 取返回信息
     * @return array
     */
    public function getReturnInfo()
    {
        return $this->_aReturnInfo;
    }

    /**
     * 取错误信息
     * @return int
     */
    public function getErrorInfo()
    {
        return $this->_sErrorInfo;
    }

    /**
     * 获取 HTTP 状态码
     *  注:若未获取到状态码或未请求则返回0
     * @return int
     */
    public function getHttpCode(){
        if ($this->_aReturnInfo){
            return (isset($this->_aReturnInfo['http_code'])) ? (int)$this->_aReturnInfo['http_code'] : 0;
        }
        else{
            return 0;
        }
    }

    /**
     * 设置代理
     * @param $sHost
     * @param $iPort
     * @param string $sType HTTP|SOCKS5
     * @param null $sUser
     * @param null $sPwd
     */
    public function setProxy($sHost,$iPort,$sType='HTTP',$sUser=NULL,$sPwd=NULL)
    {
        if($sType == 'HTTP')
        {
            $this->_sProxyType = CURLPROXY_HTTP;
        }
        if($sType == 'SOCKS5')
        {
            $this->_sProxyType = CURLPROXY_SOCKS5;
        }
        $this->_sProxyHost = $sHost;
        $this->_sProxyPort = $iPort;
        $this->_sProxyUser = $sUser;
        $this->_sProxyPwd  = $sPwd;
    }

    /**
     * 设置证书
     * @param $sCertType
     * @param $sCertFile
     * @param $sCertFilePwd
     * @param $sKeyType
     * @param $sKeyFile
     * @throws Exception
     */
    public function setSSLCert($sCertType,$sCertFile,$sCertFilePwd,$sKeyType,$sKeyFile){
        if (!is_readable($sCertFile))
        {
            throw new Exception($sCertFile.' Not Readable');
        }
        $this->_sSSLCertType = $sCertType;
        $this->_sSSLCertFile = $sCertFile;
        $this->_sSSLCertPwd  = $sCertFilePwd;

        $this->_sSSLKeyType  = $sKeyType;
        $this->_sSSLKeyFile  = $sKeyFile;
    }

    /**
     * 协议
     * @param $sUrl
     */
    public function vCheckUrl($sUrl){
        $aUrl = parse_url($sUrl);
        if($aUrl['scheme'] !== 'http' && $aUrl['scheme'] !== 'https'){
            throw new Exception('protocol should be http or https');
        }
    }

    /**
     * HTTP GET
     * @param $sUrl
     * @param array $aParams
     * @return bool|string
     * @throws Exception
     */
    public function get($sUrl,$aParams=array())
    {
        $this->vCheckUrl($sUrl);
        $aUrl = parse_url($sUrl);

        if(!empty($aUrl['port']) && ($aUrl['port'] != 443 && $aUrl['port'] != 80)){
            $sUrl = $aUrl['scheme']."://".$aUrl['host'].":".$aUrl['port'].$aUrl['path'];
        }else{
            $sUrl = $aUrl['scheme']."://".$aUrl['host'].$aUrl['path'];
        }

        if($aUrl['query'])
        {
            $sQuery = $aUrl['query']."&".http_build_query($aParams);
        }
        else
        {
            $sQuery = http_build_query($aParams);
        }
        $sUrl .= "?".$sQuery;

        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_HTTPGET,true);
        $sResult = $this->_execute($ch);
        curl_close($ch);
        return $sResult;
    }

    /**
     * HTTP POST
     * @param $sUrl
     * @param array $aParams
     * @return bool|string
     * @throws Exception
     */
    public function post($sUrl,$aParams=[])
    {
        $this->vCheckUrl($sUrl);
        $ch = curl_init($sUrl);
        curl_setopt($ch,CURLOPT_POST,true);
        if($this->_aUploadFile)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS,$this->_aUploadFile);
        }

        if($this->_sBody)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS,$this->_sBody);
        }
        else
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($aParams));
        }
        $sResult = $this->_execute($ch);
        curl_close($ch);
        return $sResult;
    }

    /**
     * HTTP PUT
     * @param $sUrl
     * @param array $aParams
     * @return bool|string
     * @throws Exception
     */
    public function put($sUrl,$aParams=[]){
        $this->vCheckUrl($sUrl);
        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aParams));
        $sResult = $this->_execute($ch);
        curl_close($ch);
        return $sResult;
    }

    /**
     * HTTP DELETE
     * @param $sUrl
     * @param array $aParams
     * @return bool|string
     * @throws Exception
     */
    public function delete($sUrl,$aParams=[]){
        $this->vCheckUrl($sUrl);
        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aParams));
        $sResult = $this->_execute($ch);
        curl_close($ch);
        return $sResult;
    }

    /**
     * HTTP HEAD
     * @param $sUrl
     * @return bool|string
     * @throws Exception
     */
    public function head($sUrl){
        $this->vCheckUrl($sUrl);
        $ch = curl_init($sUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        $sResult = $this->_execute($ch);
        curl_close($ch);
        return $sResult;
    }

    private function _execute($ch)
    {
        if($this->_sProxyType && $this->_sProxyHost && $this->_sProxyPort)
        {
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->_sProxyType);
            curl_setopt($ch, CURLOPT_PROXY, $this->_sProxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->_sProxyPort);
            if($this->_sProxyUser && $this->_sProxyPwd)
            {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_sProxyUser.":".$this->_sProxyPwd);
            }
        }

        if($this->_sSSLCertFile) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, $this->_sSSLCertType);
            curl_setopt($ch, CURLOPT_SSLCERT, $this->_sSSLCertFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->_sSSLCertPwd);

            if($this->_sSSLKeyFile){
                curl_setopt($ch, CURLOPT_SSLKEY, $this->_sSSLKeyFile);
                curl_setopt($ch, CURLOPT_SSLKEYTYPE, $this->_sSSLKeyType);
            }
        }else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if($this->_aHeader)
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_aHeader);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_iConnectTimeOut);

        //支持设置毫秒超时
        if(0 !== $this->_iMsTimeOut && $this->_iMsTimeOut > 0){
            //Refer:http://www.laruence.com/2014/01/21/2939.html
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->_iMsTimeOut);
        }else{
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->_iTimeOut);
        }

        curl_setopt($ch, CURLOPT_HEADER, 1);//显示返回头
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回值以变量显示
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);//返回结果，不自动输出
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS,3);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_VERBOSE,0);
        curl_setopt($ch, CURLOPT_NOSIGNAL,1);
        curl_setopt($ch, CURLOPT_ENCODING,'');
        curl_setopt($ch, CURLOPT_USERAGENT,$this->_sUserAgent);
        curl_setopt($ch, CURLOPT_COOKIE,$this->_sCookie);

        $sContent = curl_exec($ch);
        $this->_aReturnInfo = curl_getinfo($ch);
        $this->_sErrorInfo = curl_error($ch);

        $this->_sResponseHeader = substr($sContent, 0, $this->_aReturnInfo['header_size']);

        return substr($sContent, $this->_aReturnInfo['header_size']);
    }
}