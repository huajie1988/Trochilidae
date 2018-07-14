<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8/008
 * Time: 12:42
 */

namespace Trochilidae\bin\Lib;


class Http
{
    private static $headers=[];
    public static $statusCode=200;
    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

    public static function response($status,$content,$headers=[]){
        self::$headers=array_merge(self::$headers,$headers);
        self::$statusCode=$status;
        header('HTTP/1.1 '.$status.' '.self::$statusTexts[$status]);
        foreach (self::$headers as $key=>$header) {
            header($key.': '.$header);
        }
        echo PHP_EOL;
        echo $content;
    }

    public static function request($url,$method='GET',$request_data=[],$headers=[],$ssl_config=[]){
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);

        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if(!empty($headers)){
            curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
        }

        if(isset($ssl_config['verifyhost']))
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl_config['verifyhost']); // 从证书中检查SSL加密算法是否存在
        if(isset($ssl_config['verifypeer']))
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_config['verifypeer']); // 对认证证书来源的检查


        switch($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        //执行命令
        $response  = curl_exec($curl);
//        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        //关闭URL请求
        curl_close($curl);

        return $response;
    }

    public function setHeader($key,$value,$replace=true){
        $key = str_replace('_', '-', strtolower($key));
        $value = array_values((array) $value);

        if (true === $replace || !isset($this->headers[$key])) {
            self::$headers[$key] = $value;
        } else {
            self::$headers[$key] = array_merge(self::$headers[$key], $value);
        }
        return $this;
    }

}