<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\BorrowDetail;
use App\User;
use Session;

class HomeController extends Controller
{
    /**
     * Display view Homepage
     *
     * @return void
     */
    public function index()
    {
        $yearsborrow = BorrowDetail::selectRaw('year(created_at) as year')->groupBy('year')->orderBy('created_at', 'ASC')->get();
        $items = [];
        foreach ($yearsborrow as $year) {
            $items[$year->year] = $year->year;
        }
        $years = User::selectRaw('year(created_at) as year')->groupBy('year')->orderBy('created_at', 'ASC')->get();
        $itemuser = [];
        foreach ($years as $year) {
            $itemuser[$year->year] = $year->year;
        }
        return view('admin.chart', compact('items', $items, 'itemuser', $itemuser));
    }
    /**
     * Get API for chart.
     *
     * @param \Illuminate\Http\Request $request request
     *
     * @return \Illuminate\Http\Response
     */
    public function getApiBorrow(Request $request)
    {
        $data=$request->all();
        if (!$data) {
            return redirect()->route('home.admin');
        } else {
            $result = BorrowDetail::selectRaw('count(id) as "total", Date(created_at) as "datecreate", count(distinct(borrow_id)) as quantitys')->groupBy('datecreate')->orderBy('created_at', 'ASC')->get();
            if (!$result) {
                Session::flash('danger', trans('labels.danger'));
            }
            $listmonth=[];
            for ($i=0; $i<count($result); $i++) {
                $date=$result[$i]['datecreate'];
                $years=substr($date, config('define.begin_cut_year'), config('define.cut_year'));
                if ($years==$data['yearborrow']) {
                    $listmonth[]=$result[$i];
                }
            }
            $listday=[];
            for ($j=0; $j<count($listmonth); $j++) {
                $date1=$listmonth[$j]['datecreate'];
                $months=substr($date1, config('define.begin_cut_month'), config('define.cut_month'));
                if ($months==$data['monthborrow']) {
                    $listday[]=$listmonth[$j];
                }
            }
            for ($t=0; $t<count($listday); $t++) {
                $datareturn[$t]=['total'=>  $listday[$t]['total'],
                              'datecreate'=> $listday[$t]['datecreate'],
                              'quantitys' => $listday[$t]['quantitys']
                            ];
            }
            if (!empty($datareturn)) {
                return response()->json($datareturn, config('define.success'));
            } else {
                return response()->json([trans('labels.mes')=>trans('labels.infomation')]);
            }
        }
    }
    /**
     * Get API for chart.
     *
     * @param \Illuminate\Http\Request $request request
     *
     * @return \Illuminate\Http\Response
     */
    public function getApiUser(Request $request)
    {
        $data=$request->all();
        if (!$data) {
            return redirect()->route('home.admin');
        } else {
            $result = User::selectRaw('count(id) as "userid", Date(created_at) as "created"')->groupBy('created')->orderBy('created', 'ASC')->get();
            if (!$result) {
                Session::flash('danger', trans('labels.danger'));
            }
            $listmonth=[];
            for ($i=0; $i<count($result); $i++) {
                $date=$result[$i]['created'];
                $years=substr($date, config('define.begin_cut_year'), config('define.cut_year'));
                if ($years==$data['year']) {
                    $listmonth[]=$result[$i];
                }
            }
            $listmonthname=[];
            for ($h=0; $h<count($listmonth); $h++) {
                $listmonthname[]=substr($listmonth[$h]['created'], config('define.begin_cut_month'), config('define.cut_month'));
            }
            $result1=array_count_values($listmonthname);
            $key=array_keys($result1);
            for ($t=0; $t<count($key); $t++) {
                $datareturn[]=['userid'=>  $result1[$key[$t]],
                              'created'=> $key[$t]
                            ];
            }
            if (!empty($datareturn)) {
                return response()->json($datareturn, config('define.success'));
            } else {
                return response()->json([trans('labels.mes')=>trans('labels.infomation')]);
            }
        }
    }
}
