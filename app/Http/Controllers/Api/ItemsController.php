<?php

namespace App\Http\Controllers\Api;

use App\Models\insurence;
use App\Models\Item;
use App\Models\SchedulModel;
use App\Models\update_link;
use App\Settings;
use Carbon\Carbon;
use DOMDocument;
use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemsController extends Controller
{
    public function validatePoNumber(Request $request)
    {
        \Log::info([$request->all()]);
        $poNo = $request->input('policyNo');
        if ($request->has('userName')) {
            $user = $request->input('userName');
        } else {
            $user = auth()->user()->email;
        }

        return validatePoNo($poNo, $user);
    }

    public function createItem(Request $request)
    {
        \Log::info([$request->all()]);
        $customerMobile = $request->input('customerMobile');
        try {
            $settings = Settings::where('name', 'isGenerateLink')->first()->value;

            if ($settings == 'true') {
                $existRecurringItem = insurence::where('police_no', $request->input('policeNo'))
                    ->where('interval', '!=', '')
                    ->where('status', 'active')
                    ->first();

                $year = Carbon::now()->format('y');
                $month = Carbon::now()->month;

                if ($existRecurringItem && !empty($request->input('interval'))) {

                    //check card expire
                    $checkCardExp = insurence::where('id', $existRecurringItem->id)
                        ->where(\DB::raw('substring_index(card_exp, \'-\', -1)'), '<=', $year)
                        ->where(\DB::raw('substring_index(card_exp, \'-\', 1)'), '<=', $month)
                        ->first();

                    if($checkCardExp){
                        try {
                            $updateLink = new update_link;
                            $updateLink->user = auth()->user()->email;
                            $updateLink->ins_id = $checkCardExp->id;
                            $updateLink->linkId = itemUrl();
                            $updateLink->cardType = 'PRIMARY';
                            $updateLink->status = update_link::CREATE;
                            $updateLink->created_at = Carbon::now();
                            $updateLink->updated_at = Carbon::now();
                            $updateLink->save();

                            $updateLink->customerMobile = $customerMobile;

                            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/update/$updateLink->linkId";

                            $smsStatus = \masterCardService::sendSms($customerMobile,
                                "Update link " . $url);
                            $smsRes = json_decode($smsStatus, true);

                            $smsStatus = $smsRes['status'] == '1001' ? 'Success' : 'Failed';

                            $updateLink->status = $smsStatus == 'Success' ? update_link::CREATE : update_link::SMS_FAILED;
                            $updateLink->save();

                            return response()->json([
                                'status' => 200,
                                'data' => [
                                    'status' => 'Success'
                                ]
                            ]);
                        } catch (\Exception $exception) {
                            \Log::info($exception->getMessage());
                            return [
                                'status' => 400,
                                'message' => 'Cannot Create Insurance.'
                            ];
                        }
                    } else {
                        return [
                            'status' => 400,
                            'message' => 'This policy number already created!'
                        ];
                    }
                } else {
                    date_default_timezone_set("Asia/Colombo");
                    $user = $request->input('userName');
                    $customerName = $request->input('customerName');
                    $customerEmail = $request->input('customerEmail');
                    $amount = $request->input('amount');
                    $policeNo = $request->input('policeNo');
                    $interval = $request->input('interval');
                    $startDate = $request->input('startDate');
                    $premium = $request->input('premium');
                    $amount = str_replace(",", "", $amount);

                    $item = new Item;
                    $item->user = $user;
                    $item->customerName = $customerName;
                    $item->customerEmail = $customerEmail;
                    $item->customerMobile = $customerMobile;
                    $item->amount = $amount;
                    $item->policeNo = $policeNo;
                    $item->interval = $interval;
                    $item->startDate = $startDate;
                    $item->linkId = itemUrl();
                    $item->expire = 0;
                    $item->status = 0;
                    $item->premium = $premium;
                    $item->created_at = Carbon::now();
                    $item->save();

                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/pay/$item->linkId";

                    \masterCardService::sendSms($customerMobile, "Thank you for choosing AIA Insurance! Set up an auto-pay instruction to pay premiums from your debit/credit card.Tap here " . $url);

                    return response()->json([
                        'status' => 200,
                        'data' => [
                            'status' => 'Success'
                        ]
                    ]);
                }
            } else {
                \Log::info('link create disable');
                return response()->json([
                    'status' => 400,
                    'data' => [
                        'code' => 'ServerException',
                        'message' => 'Cannot Create Insurance.'
                    ]
                ]);
            }
        } catch (\Exception $exception) {
            \Log::info($exception->getMessage());
            return response()->json([
                'status' => 400,
                'data' => [
                    'code' => 'ServerException',
                    'message' => 'Cannot Create Insurance.'
                ]
            ]);
        }
    }

    public function createItemFromExcel(Request $request)
    {
        \Log::info(json_encode($request->all()));
        $getRealPath = $request->file('file')->getRealPath();
        $data = Excel::load($getRealPath)->get();
        $headerRow = $data->first()->keys()->toArray();
        $dataArr = [];

        foreach ($data as $key => $value) {

            $premium = $value['premium'];
            $policyNo = $value['policy_no'];
            $startDate = $value['start_date'];
            $amount = $value['amount'];
            $interval = $value['interval'];
            $isRecurring =  isset($value['is_recurring']) ? $value['is_recurring'] : 1 ;

            $item = new Item;
            $item->user = 'admin@admin.com';

            $item->amount = $amount;
            $item->policeNo = $policyNo;
            $item->interval = $interval;
            $item->startDate = Carbon::parse($startDate)->format('Y-m-d');
            $item->customerMobile = '+94712345432';
            $item->linkId = itemUrl();
            $item->expire = 0;
            $item->premium = $premium;
            $item->preAmount = $amount;
            $item->status = Item::CREATE;
            $item->isDontExp = true;
            $item->isRecurring = $isRecurring;
            $item->created_at = Carbon::now();
            $item->isExcel = 1;
            $item->save();

            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/paymentsignup/$item->linkId";

            $data = [
                $premium,
                $policyNo,
                $interval,
                $startDate,
                $amount,
                $url
            ];

            array_push($dataArr, $data);
        }

        //\Log::info($dataArr);
        $fileName = Carbon::now()->format('d_m_y');

        Excel::create($fileName, function ($excel) use ($dataArr) {

            $excel->sheet('mySheet', function ($sheet) use ($dataArr) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('PREMIUM');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('POLICY_NO');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('INTERVAL');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('START_DATE');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('AMOUNT');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('LINK');
                });

                //\Log::info(json_encode($dataArr));

                if (!empty($dataArr)) {
                    for ($i = 0; $i < count($dataArr); $i++) {
                        $z = $i + 2;
                        $sheet->cell('A' . $z, $dataArr[$i][0]);
                        $sheet->cell('B' . $z, $dataArr[$i][1]);
                        $sheet->cell('C' . $z, $dataArr[$i][2]);
                        $sheet->cell('D' . $z, $dataArr[$i][3]);
                        $sheet->cell('E' . $z, $dataArr[$i][4]);
                        $sheet->cell('F' . $z, $dataArr[$i][5]);
                    }
                }
            });

        })->store('xls', storage_path('Excel'));

        return response()->download(storage_path('Excel/'.$fileName.'.xls'));
        //\Log::info([$data]);
    }

}
