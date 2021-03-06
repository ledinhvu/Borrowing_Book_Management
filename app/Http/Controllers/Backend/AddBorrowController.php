<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Borrow;
use App\Book;
use App\BookItem;
use App\BorrowDetail;
use Carbon\Carbon;
use DB;
use Auth;

class AddBorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.borrows.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request\Request $request input value
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data=$request->all();
        try {
            $bookitem=BookItem::findOrfail($data['bookID']);
            $book=Book::findOrfail($bookitem['book_id']);
            try {
                $borrowDetailItem=BorrowDetail::where('book_item_id', $bookitem['id'])->first();
                if ($borrowDetailItem['status']== config('define.not_give_back')) {
                    return response()->json(['mes'=> trans('borrow.book_borrowed')]);
                } else {
                    $bookitem['bookname']=$book['name'];
                    return response()->json($bookitem);
                }
            } catch (ModelNotFoundException $ex) {
                $bookitem['bookname']=$book['name'];
                return response()->json($bookitem);
            }
        } catch (ModelNotFoundException $ex) {
            return response()->json(['mes'=> trans('borrow.book_not_exist')]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request\Request $request input value
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$request->all();
        $borrow=Borrow::create(['user_id'=>$data['listBook']['0'],
                                'admin_user_id' => Auth::guard('admin')->user()->id,
                                'quantity' => count($data['listBook'])-1
                               ]);
        $borrowDetail = array();
        for ($i=1; $i < count($data['listBook']); $i++) {
            $current = Carbon::now();
            $expireTime = $current->addDays(config('define.limit_day'));
            array_push($borrowDetail, [
                'borrow_id' => $borrow['id'],
                'book_item_id' => $data['listBook'][$i],
                'status' => config('define.not_give_back'),
                'expiretime' => $expireTime ,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        $list=BorrowDetail::insert($borrowDetail);
        if ($list) {
            return response()->json(['mes'=>trans('borrow.success')]);
        } else {
            return response()->json(['mes'=>trans('borrow.fail')]);
        }
    }

    /**
     * Check and show info user.
     *
     * @param string $username username
     *
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        
        $user=User::where('username', $username)->first();
        if (!empty($user)) {
            $borrow=Borrow::where('user_id', $user['id'])->get();
            $total=0;
            foreach ($borrow as $item) {
                $total+=$item['quantity'];
            }
            if ($total< config('define.max_borrow')) {
                return response()->json(['mes' => trans('borrow.user_allow'),
                                         'allow' => trans('borrow.true'),
                                         'quantity' => (config('define.max_borrow')-$total),
                                         'user_id' => $user['id']
                                    ]);
            } else {
                return response()->json(['mes'=> trans('borrow.max_borrow'),
                                         'allow'=> trans('borrow.false')
                    ]);
            }
        } else {
            return response()->json(['mes'=> trans('borrow.no_user')]);
        }
    }
}
