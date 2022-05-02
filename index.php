<?php
declare(strict_types=1);

class URLFetcher
{
    public string $content = '';
    private $fp;

    public function __construct(
        private string $url,
        private Closure $done,
        private Closure $onerror
    ) {}

    public function start():void
    {
        $this->fp = @stream_socket_client("tcp://$this->url:80",$errno,$errstr,30);
        if(!$this->fp){
            ($this->onerror)($errstr);
        }

        stream_set_blocking($this->fp,false);
        fwrite($this->fp,"GET / HTTP/1.0\r\nHost: $this->url\r\nAccept: */*\r\n\r\n");
        Loop::add(fn() => $this->tick());
    }
    public function tick():void
    {
        if($this->isDone()){
            fclose($this->fp);
            ($this->done)($this->content);
        }else{
            $this->readSomeBytes();
            Loop::add(fn()=>$this->tick());
        }
    }

    public function readSomeBytes():void
    {
        $this->content = fgets($this->fp,100);
    }

    public function isDone():bool
    {
        return feof($this->fp);
    }

}

class Loop
{
    private static array $callbacks = [];

    public static function add(callable $callback)
    {
        self::$callbacks[] = $callback;
    }

    public static function run()
    {
        while(count(self::$callbacks)){
            $cb = array_shift(self::$callbacks);
            $cb();
        }
    }


}

function fetchUrl(string $url,callable $done,callable $onerror){

    $fetcher =  new URLFetcher(
        $url,
        Closure::fromCallable($done),
        Closure::fromCallable($onerror)
    );
    Loop::add(fn()=> $fetcher->start());
}

Loop::add(function (){
    echo "Fetching dir.bg\n";
    fetchUrl(
        'www.dir.bg',
        function (string $dirbg){
            echo "Got dir.bg\n";
            echo $dirbg;
        },
        function (string $err){
            echo "Got error from dir.bg\n";
            echo "err\n";
        }
    );

    echo "fetching google.com\n";
    fetchUrl(
    'www.google.com',
        function (string $google){
            echo "Got Google\n";
            echo "Size is: " . strlen($google) . "\n";
        },
        function (string $err){
            echo "Got error from google\n";
            echo "err\n";
        }
    );
});

Loop::run();