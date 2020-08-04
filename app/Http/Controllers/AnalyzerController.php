<?php

namespace App\Http\Controllers;

use App\UrlClickCount;
use App\UserClickCountBrowser;
use App\UserClickCountMobile;
use DateTime;
use Illuminate\Http\Request;

class AnalyzerController extends Controller
{
    /**
     * @param Request $request contains parameters url_short,report_type,
     * is_mobile,browser are optional
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function clickReport(Request $request){
        $this->validate($request, [
            'url_short' => 'required',
            'report_type' => 'required'
        ]);
        $selector = $this->_reportSelector($request);
//        if ($selector == 'today_count'){
//            $today_finish = new DateTime();
//            $today_start = new DateTime();
//            $today_start->setTime(00, 00, 00);
//
//
//        }
        if (isset($request->is_mobile)){
            $answer = UrlClickCount::where('is_mobile',$request->is_mobile)
                ->where('url_short',$request->url_short)
                ->sum($selector);
            return response()->json($answer);
        }
        if(isset($request->browser)){
            $answer = UrlClickCount::where('browser',$request->browser)
                ->where('url_short',$request->url_short)
                ->sum($selector);
            return response()->json($answer);
        }
        $answer = UrlClickCount::where('url_short',$request->url_short)
            ->sum($selector);
        return response()->json($answer);

    }

    public function userReport(Request $request){
        $this->validate($request, [
            'url_short' => 'required',
            'report_type' => 'required'
        ]);
        $selector = $this->_reportSelector($request);
        if (isset($request->is_mobile)){
            $answer = UserClickCountMobile::where('is_mobile',$request->is_mobile)
                ->where('url_short',$request->url_short)
                ->select($selector)->first();
            return response()->json($answer);
        }
        if(isset($request->browser)){
            $answer = UserClickCountBrowser::where('browser',$request->browser)
                ->where('url_name',$request->url_short)
                ->select($selector)->first();
            return response()->json($answer);
        }
        $answer = UserClickCountMobile::where('url_short',$request->url_short)
            ->sum($selector);
        return response()->json($answer);

    }

    private function _reportSelector(Request $request)
    {
        switch ($request->report_type){
            case 1:
                return 'today_count';
            case 2:
                return 'daily_count';
            case 3:
                return 'weekly_count';
            case 4:
                return 'monthly_count';
        }
    }


}
