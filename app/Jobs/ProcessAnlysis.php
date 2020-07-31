<?php

namespace App\Jobs;

use App\Click;
use App\Url;
use App\UrlClickCount;
use http\Cookie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ProcessAnlysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url_name;
    protected $browser;
    protected $is_mobile;
    protected $url;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @param string $url_name
     * @param Request $request
     * @param Url|null $url
     */
    public function __construct(string $url_name,
                                string $browser,
                                bool $is_mobile,
                                string $user_id,
                                Url $url=null)
    {
        $this->url = $url;
        $this->browser = $browser;
        $this->is_mobile = $is_mobile;
        $this->url_name = $url_name;
        $this->user_id = $user_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::funnel('key')->block(0)->limit(1)->then(function () {
            //check click count
            if($this->url){
                if($this->url->click_count <=10){
                    $this->url->click_count++;
                    $this->url->save();
                }else{
                    Redis::set($this->url->short_url,$this->url->long_url);
                }
            }

            $clicks =new Click();
            $clicks->is_mobile= $this->is_mobile;
            $clicks->browser= $this->browser;
            $clicks->user_clicker=$this->user_id;
            $clicks->url_name=$this->url_name;
            $clicks->save();
            //save to today's clicks
//            $ucc = UrlClickCount::where('is_mobile',$this->is_mobile)->where('browser',$clicks->browser)->first();
//            if ($ucc){
//                $ucc->today_count+=1;
//                $ucc->save();
//            }
        }, function () {
            return $this->release(1);
        });
    }
}
