<?php


class WebClient
{
    private $last_body = null;
    private $delay = 0;
    private $handle = null;
    private $default_headers = null;

    private $referer = null;
    private $auto_referer = false;

    private $headers = null;
    private $enctype = null;

    private $log_prefix = null;
    private $log_step = 0;

    private $response_headers = array();


    protected static $std_headers = array(
        'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.10) Gecko/2009042315 Firefox/3.0.10',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3',
        'Accept-Encoding: ',
        'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
        'Keep-Alive: 24000',
        'Connection: keep-alive',
        );


    public function __construct()
    {
        $this->setDefaultHeaders($this->getStandardHeaders());
    }


    public function createHandle()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/dev/null');

        // HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);

        return $ch;
    }


    public function setVerbose($verbose = true)
    {
        curl_setopt($this->getHandle(), CURLOPT_VERBOSE, $verbose);
    }


    public function init()
    {
        $this->setHeaders(null);
        $this->setEncType(null);
    }


    public function setAjax()
    {
        $this->addHeader('X-Requested-With: XMLHttpRequest');
    }


    protected function log($data, $suffix)
    {
        if ($this->log_prefix === null)
            return;

        $path = $this->log_prefix.$suffix;
        $bytes = file_put_contents($path, $data, FILE_APPEND);
        if ($bytes === false)
            throw new Exception("Error writing to log file '$path' !");
    }


    // {{{ Accessors

    public static function getStandardHeaders()
    {
        return self::$std_headers;
    }


    public function getDefaultHeaders()
    {
        return $this->default_headers;
    }


    public function setDefaultHeaders($headers)
    {
        $this->default_headers = $headers;
    }


    public function getReferer()
    {
        return $this->referer;
    }


    public function setReferer($url)
    {
        $this->referer = $url;
    }


    public function getAutoReferer()
    {
        return $this->auto_referer;
    }


    public function setAutoReferer($auto)
    {
        $this->auto_referer = $auto;
    }


    public function getDelay()
    {
        return $this->delay;
    }


    public function setDelay($delay)
    {
        $this->delay = $delay;
    }


    public function getHandle()
    {
        if (!$this->handle)
            $this->setHandle($this->createHandle());
        return $this->handle;
    }


    public function setHandle($handle)
    {
        $this->handle = $handle;
    }


    public function closeHandle()
    {
        if (!$this->handle)
            curl_close($this->handle);
        $this->handle = null;
    }


    public function getLogPrefix()
    {
        return $this->log_prefix;
    }


    public function setLogPrefix($prefix)
    {
        $this->log_prefix = $prefix;
    }


    public function getLogStep()
    {
        return $this->log_step;
    }


    public function setLogStep($step)
    {
        $this->log_step = $step;
    }


    public function getEncType()
    {
        return $this->enctype;
    }


    public function setEncType($enctype)
    {
        $this->enctype = $enctype;
    }


    public function getResponseHeaders()
    {
        return $this->response_headers;
    }


    public function getLastBody()
    {
        return $this->last_body;
    }

    // }}}

    // {{{ Headers functions

    public function getHeaders()
    {
        if ($this->headers === null)
            $this->headers = $this->getDefaultHeaders();
        return $this->headers;
    }


    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }


    public function addHeader($header, $replace = true)
    {
        $pos = $this->headerPos($header);
        if ($pos !== false && !$replace)
            return;

        if ($this->headers === null)
            $this->headers = $this->getDefaultHeaders();

        if ($pos !== false)
            $this->headers[$pos] = $header;
        else
            $this->headers[] = $header;
    }


    public function removeHeader($header)
    {
        $pos = $this->headerPos($header);
        if ($pos === false)
            return;

        if ($this->headers === null)
            $this->headers = $this->getDefaultHeaders();
        unset($this->headers[$pos]);
    }


    protected function headerPos($header)
    {
        $len = strpos($header, ':');
        if ($len === false)
            $len = strlen($header);
        else
            $len = $len + 1;

        foreach ($this->getHeaders() as $index => $value)
            if (strncmp($header, $value, $len) === 0)
                return true;

        return false;
    }

    // }}}

    // {{{ HTTP Request functions

    public function get($url)
    {
        $this->log("GET $url\n", '-step'.$this->getLogStep().'-get.log');
        $body = $this->request($url);
        $this->checkReturnCode();
        return $body;
    }


    public function post($url, $post)
    {
        if ($this->getEncType() === 'multipart/form-data')
            $data = $this->buildMultipartPost($post);
        elseif ($this->getEncType() === 'application/x-www-form-urlencoded' || $this->getEncType() === null)
            $data = $this->buildUrlencodedPost($post);
        elseif ($this->getEncType() === 'raw')
            $data = $post;
        else
            throw new Exception("Unknown enctype='{$this->getEncType()}'");

        $ch = $this->getHandle();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);

        $this->log("POST $url\r\n\r\n".$data."\r\n", '-step'.$this->getLogStep().'-post.log');;
        $body = $this->request($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_POST, false);

        $this->checkReturnCode();
        return $body;
    }


    /**
     * Fonction PUT
     */
    public function put($url, $data)
    {
        $fh = fopen('php://memory', 'rw');
        fwrite($fh, $data);
        rewind($fh);

        $ch = $this->getHandle();
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($data));

        $this->log("PUT $url\r\n\r\n".$data."\r\n", '-step'.$this->getLogStep().'-put.log');
        $body = $this->request($url);

        curl_setopt($ch, CURLOPT_INFILESIZE, 0);
        curl_setopt($ch, CURLOPT_PUT, false);
        fclose($fh);

        $this->checkReturnCode();
        return $body;
    }


    public function delete($url)
    {
        $ch = $this->getHandle();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $this->log("DELETE $url\n", '-step'.$this->getLogStep().'-delete.log');
        $body = $this->request($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, '');

        $this->checkReturnCode();
        return $body;
    }


    public function checkReturnCode()
    {
        $http_code = $this->getReturnCode();
        if ($http_code < 200 || $http_code >= 300)
            throw new Exception("Request error, HTTP $http_code received: {$this->getLastHttpResponse()}");
    }


    public function getReturnCode()
    {
        $ch = $this->getHandle();
        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }


    public function getLastHttpResponse()
    {
        $response = '';
        foreach ($this->getResponseHeaders() as $header)
            if (strpos($header, 'HTTP/') === 0)
                $response = $header;
        return $response;
    }


    public function buildUrlencodedPost($post)
    {
        $data = '';
        foreach ($post as $name => $value) {
            $name = urlencode($name);

            if (is_array($value)) {
                foreach ($value as $v) {
                    if ($data)
                        $data .= '&';
                    $data .= $name.'='.urlencode((string) $v);
                }
            } else {
                if (isset($value[0]) && $value[0] == '@')
                    throw new Exception("Cannot send file in application/x-www-form-urlencoded form");
                if ($data)
                    $data .= '&';
                $data .= $name.'='.urlencode((string) $value);
            }
        }
        return $data;
    }


    protected function buildFilePostPart($boundary, $name, $value)
    {
        $data = "--$boundary\r\n";

        $args = substr($value, 1);
        $args_array = explode(';', $args);
        $file = $args_array[0];
        $filename = basename($file);
        foreach ($args_array as $arg) {
            @list($arg_name, $arg_value) = explode("=", $arg);
            if ($arg_name == "filename")
                $filename = ($arg_value ? $arg_value : '');
        }
        $data .= "Content-Disposition: form-data; name=\"$name\"; filename=\"".$filename."\"\r\n";
        $data .= "Content-Type: application/octet-stream\r\n";
        $data .= "\r\n";
        if ($file)
            $data .= file_get_contents($file);
        $data .= "\r\n";

        return $data;
    }


    protected function buildPostPart($boundary, $name, $value)
    {
        if (isset($value[0]) && $value[0] === '@')
            return $this->buildFilePostPart($boundary, $name, $value);

        $data = "--$boundary\r\n";
        $data .= "Content-Disposition: form-data; name=\"$name\"\r\n";
        $data .= "\r\n";
        $data .= $value."\r\n";

        return $data;
    }


    protected function buildMultipartPost($post)
    {
        $boundary = str_repeat('-', 28).myFunctions::generateRandomKey(12, '0123456789abcdef');

        $this->addHeader('Content-Type: multipart/form-data; boundary='.$boundary);
        $this->addHeader('Expect:');

        $data = '';
        foreach ($post as $name => $value) {
            if (is_array($value))
                foreach ($value as $v)
                    $data .= $this->buildPostPart($boundary, $name, $v);
            else
                $data .= $this->buildPostPart($boundary, $name, $value);
        }
        $data .= "--$boundary--\r\n";
        return $data;
    }


    public function request($url)
    {
        $ch = $this->getHandle();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($this->getReferer())
            $this->addHeader('Referer: '.$this->getReferer(), false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());

        curl_setopt($this->getHandle(), CURLOPT_HEADERFUNCTION, array($this, 'readHeader'));
        $this->response_headers = array();

        $delay = $this->getDelay();
        if ($delay)
            sleep($delay);

        $body = curl_exec($ch);
        $this->last_body = $body;

        $this->log($body, '-step'.$this->getLogStep().'-response.html');
        $this->log_step++;

        if (curl_errno($ch))
            throw new Exception("cURL error ".curl_errno($ch).": ".curl_error($ch));

        if ($this->getAutoReferer())
            $this->setReferer($url);

        $this->init();
        return $body;
    }


    protected function readHeader($url, $str)
    {
        $this->response_headers[] = $str;
        return strlen($str);
    }


    public function setHTTPAuth($username, $password, $method = CURLAUTH_BASIC)
    {
        $ch = $this->getHandle();

        curl_setopt($ch, CURLOPT_HTTPAUTH, $method);
        curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
    }
    // }}}


    public function guessCaptcha($url)
    {
        $body = $client->get($url);

        $file0 = tempnam(SF_ROOT_DIR.'/temp', 'captcha');
        $dir = $file0.'.dir';
        mkdir($dir);
        $file1 = $dir.'/img1.jpg';
        $file2 = $dir.'/img2.jpg';
        $result = array();

        file_put_contents($file1, $body);
        exec('convert '.escapeshellarg($file1).' -blur 2 -threshold 40% '.escapeshellarg($file2));
        exec('gocr '.escapeshellarg($file2), $result);

        unlink($file2);
        unlink($file1);
        rmdir($dir);
        unlink($file0);

        return $result[0];
    }
}

?>
