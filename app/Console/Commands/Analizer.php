<?php

namespace App\Console\Commands;

use App\Click;
use App\UrlClickCount;
use App\UserClickCountBrowser;
use App\UserClickCountMobile;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Analizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyzer:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is for chaching analyzer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *  this command must run at 12 am
     * @return mixed
     */
    public function handle()
    {
        //calculating dates
        $today_finish = new DateTime();
        $today_finish->setTime(23, 59, 59);
        $today_start = new DateTime();
        $today_start->setTime(00, 00, 00);
        $last_seven_days = new DateTime();
        $last_seven_days->modify("-7 days");
        $today_start->setTime(00, 00, 00);
        $last_month = new DateTime();
        $last_month->modify("-30 days");
        $last_month->setTime(00, 00, 00);
        //generate click data
        UserClickCountBrowser::truncate();
        UserClickCountMobile::truncate();
        UrlClickCount::truncate();
        DB::insert("
                INSERT INTO url_click_count( is_mobile, browser, url_short
                                   , daily_count, weekly_count, monthly_count)
        select *
        from (select count(*) as monthly, is_mobile, browser, url_name
              from `clicks`
              where `created_at` <= ?
                and `created_at` >= ?
              group by is_mobile, browser, url_name) A
                 natural left join
             (select count(*) as weekly, is_mobile, browser, url_name
              from `clicks`
              where `created_at` <= ?
                and `created_at` >= ?
              group by is_mobile, browser, url_name) B
                 natural left join
             (select count(*) as daily, is_mobile, browser, url_name
              from `clicks`
              where `created_at` <= ?
                and `created_at` >= ?
              group by is_mobile, browser, url_name) C",
            [$today_finish, $last_month, $today_finish, $last_seven_days, $today_finish, $today_start]);

        DB::insert("INSERT INTO user_click_count_mobile( is_mobile, url_short
                                   , daily_count, weekly_count, monthly_count)
select *
from (select count(distinct user_clicker) as monthly, is_mobile, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-06-30 00:00:00'
      group by is_mobile, url_name) A
         natural left join
     (select count(distinct user_clicker) as weekly, is_mobile, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-07-23 00:00:00'
      group by is_mobile, url_name) B
         natural left join
     (select count(distinct user_clicker) as daily, is_mobile, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-07-30 00:00:00'
      group by is_mobile, url_name) C",
            [$today_finish, $last_month, $today_finish, $last_seven_days, $today_finish, $today_start]);
        DB::insert("INSERT INTO user_click_count_browser( browser, url_name
                                   , daily_count, weekly_count, monthly_count)
select *
from (select count(distinct user_clicker) as monthly, browser, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-06-30 00:00:00'
      group by browser, url_name) A
         natural left join
     (select count(distinct user_clicker) as weekly, browser, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-07-23 00:00:00'
      group by browser, url_name) B
         natural left join
     (select count(distinct user_clicker) as daily, browser, url_name
      from `clicks`
      where `created_at` <= '2020-07-30 23:59:59'
        and `created_at` >= '2020-07-30 00:00:00'
      group by browser, url_name) C",
            [$today_finish, $last_month, $today_finish, $last_seven_days, $today_finish, $today_start]);
    }


}
