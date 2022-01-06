<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Auth;
use DB;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class FRQAPIController extends Controller
{
    public function callAPI($method, $url, $data){
      $curl = curl_init();

      switch ($method){
          case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
              curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
          case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
              curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
          default:
            if ($data)
              $url = sprintf("%s?%s", $url, http_build_query($data));
       }

       // OPTIONS:
       curl_setopt($curl, CURLOPT_URL, $url);
       curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'APIKEY: 111111111111111111111',
          'Content-Type: application/json',
       ));

       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

       // EXECUTE:
       $result = curl_exec($curl);
       if(!$result){die("Connection Failure");}
       curl_close($curl);
       return $result;
    }

    public function getList(Request $request) {

      $frq_Info = DB::table('Inquiry')
                ->orderby("InquiryDate",'desc')
                ->take(20)
                ->get()
                ->random(1);

      echo $frq_Info;

    }

    public function getList_test(Request $request) {

      $Json_package = Array (
          "deadline"     => "test",
          "targets"      => Array (
                              "testA",
                              "testB",
                              "testC"
                            ),
          "transactions" => Array (
              "inquiryCompanyName" => "test",
              "inquiryDate"        => "test",
              "inquiryPerson"      => "test",
              "inquiryId"          => "test",
              "deliveryDate"       => "test",
              "deliveryPlace"      => "test",
              "Note"               => "test",
              "inquiryDetail"      => [Array (
                  "inquiryItemId"         => "test",
                  "inquiryItemName"       => "test",
                  "inquiryItemQuantity"   => "test",
                  "inquiryItemUnit"       => "test",
              )],
          ),
      );



      $Json_package = json_encode($Json_package);
      // echo $Json_package;

      // $make_call = callAPI('POST', 'http://35.223.39.192/api/inquiry/send', $Json_package);
      // $make_call = $this->callAPI('POST', 'http://127.0.0.1/ERP_demoSite/public/api/frq/getList/pk', $Json_package);
      // echo $make_call;
      // echo "<br>";

      $client = new \GuzzleHttp\Client([
          'headers' => ['Content-Type' => 'application/json']
      ]);
      //
      $res = $client->request('POST', 'http://35.223.39.192/api/inquiry/send', [
                          'body' => $Json_package,
                      ]);

      echo $res->getBody();
      // $response = Http::acceptJson()->post('http://35.223.39.192/api/inquiry/send', $Json_package);
      // echo json_encode($response->json());
    }

    public function getList_pk(Request $request) {
        // ---------------- query for [inquiry Company Name] and [delivery Place]
        $com_Info = DB::table('CompanyAccount')
                      ->first();

        $inquiryCompanyId   = $com_Info->CompanyAccountID;
        $inquiryCompanyName = $com_Info->CompanyAccountFullName;
        $deliveryPlace      = $com_Info->CompanyAccountAddress;

        // ---------------- query for FRQ infomation
        // primary key
        $InquiryID  = '21120004';

        $frq_Info = DB::table('Inquiry')
                      ->where("InquiryID" ,$InquiryID)
                      ->get();

        $inquiryDate = date("Y-m-d",strtotime($frq_Info[0]->InquiryDate));

        // ---------------- query for [Employee name]
        $emp_Info = DB::table('Employee')
                      ->where("EmployeeID" ,$frq_Info[0]->EmployeeID)
                      ->first();

        $inquiryPerson = $emp_Info->EmployeeName;


        // 測試資料替換 **********************************************************
        // $inquiryCompanyName = "companya";
        // $inquiryCompanyId = "001";
        // ***********************************************************************

        // ---------------- create Array to store json Package
        $jsonPackage = [];

        $frq_detail_Infos = DB::table('InquiryDetail')
                             ->where("InquiryID" ,$InquiryID)
                             ->get();

        foreach ($frq_detail_Infos as $frq_detail_Info) {

          $frq_detail_vendor_Infos = DB::table('InquiryDetailVendor')
                                       ->where("InquiryID",  $frq_detail_Info->InquiryID)
                                       ->where("InquirySeq", $frq_detail_Info->InquirySeq)
                                       ->get();

          foreach ($frq_detail_vendor_Infos as $frq_detail_vendor_Info) {

              $vendor_Infos = DB::table('Vendor')
                            ->where("VendorID" ,$frq_detail_vendor_Info->VendorID)
                            ->first();

              // $vendorPayTerm
              $vendorName    = $vendor_Infos->VendorFullName;
              $vendorPayTerm = $vendor_Infos->PaymentTermID;

              // 測試資料替換 ****************************************************
              $vendorName = "companyb";
              // *****************************************************************

              //prepare Json package for message box, store in $Json_package array
              $Json_package = Array (
                  "deadline"     => date("Y-m-d",strtotime('+20 day')),
                  "targets"      => Array ($vendorName),
                  "transactions" => Array (
                      "inquiryCompany"   => $inquiryCompanyName,
                      "inquiryCompanyId" => $inquiryCompanyId,
                      "inquiryDate"      => $inquiryDate,
                      "inquiryPerson"    => $inquiryPerson,
                      "inquiryId"        => $InquiryID,
                      "deliveryDate"     => date("Y-m-d",strtotime($frq_detail_Info->DeliveryDay)),
                      "deliveryPlace"    => $deliveryPlace,
                      "paymentTerms"     => $vendorPayTerm,
                      "Note"             => $frq_detail_Info->InquiryDetailMemo,
                      "inquiryDetail"    => [Array (
                          "inquiryItemId"       => $frq_detail_Info->InquirySeq,
                          "inquiryItemName"     => $frq_detail_Info->MaterialName,
                          "inquiryItemQuantity" => floor($frq_detail_Info->Quantity),
                          "inquiryItemUnit"     => $frq_detail_Info->QuantityUnitID,
                      )],
                  ),
              );

              $Json_package = json_encode($Json_package);
              array_push($jsonPackage, $Json_package);

          };

        }


        echo $jsonPackage[0];


    }

    public function getOrder(Request $request) {

      // ---------------- query for [inquiry Company Name] and [delivery Place]
      $com_Info = DB::table('CompanyAccount')
                    ->first();

      $inquiryCompanyId   = $com_Info->CompanyAccountID;
      $inquiryCompanyName = $com_Info->CompanyAccountFullName;
      $deliveryPlace      = $com_Info->CompanyAccountAddress;

      // ---------------- query for FRQ infomation
      // primary key
      $InquiryID  = $request->InquiryID;

      $frq_Info = DB::table('Inquiry')
                    ->where("InquiryID" ,$InquiryID)
                    ->get();

      $inquiryDate = date("Y-m-d",strtotime($frq_Info[0]->InquiryDate));

      // ---------------- query for [Employee name]
      $emp_Info = DB::table('Employee')
                    ->where("EmployeeID" ,$frq_Info[0]->EmployeeID)
                    ->first();

      $inquiryPerson = $emp_Info->EmployeeName;


      // 測試資料替換 **********************************************************
      // $inquiryCompanyName = "companya";
      // $inquiryCompanyId = "001";
      // ***********************************************************************

      // ---------------- create Array to store json Package
      $jsonPackage = [];

      $frq_detail_Infos = DB::table('InquiryDetail')
                           ->where("InquiryID" ,$InquiryID)
                           ->get();

      foreach ($frq_detail_Infos as $frq_detail_Info) {

        $frq_detail_vendor_Infos = DB::table('InquiryDetailVendor')
                                     ->where("InquiryID",  $frq_detail_Info->InquiryID)
                                     ->where("InquirySeq", $frq_detail_Info->InquirySeq)
                                     ->get();

        foreach ($frq_detail_vendor_Infos as $frq_detail_vendor_Info) {

            $vendor_Infos = DB::table('Vendor')
                          ->where("VendorID" ,$frq_detail_vendor_Info->VendorID)
                          ->first();

            // $vendorPayTerm
            $vendorName    = $vendor_Infos->VendorFullName;
            $vendorPayTerm = $vendor_Infos->PaymentTermID;

            // 測試資料替換 ****************************************************
            $vendorName = "companyb";
            // *****************************************************************

            //prepare Json package for message box, store in $Json_package array
            $Json_package = Array (
                "deadline"     => date("Y-m-d",strtotime('+20 day')),
                "targets"      => Array ($vendorName),
                "transactions" => Array (
                    "inquiryCompany"   => $inquiryCompanyName,
                    "inquiryCompanyId" => $inquiryCompanyId,
                    "inquiryDate"      => $inquiryDate,
                    "inquiryPerson"    => $inquiryPerson,
                    "inquiryId"        => $InquiryID,
                    "deliveryDate"     => date("Y-m-d",strtotime($frq_detail_Info->DeliveryDay)),
                    "deliveryPlace"    => $deliveryPlace,
                    "paymentTerms"     => $vendorPayTerm,
                    "Note"             => $frq_detail_Info->InquiryDetailMemo,
                    "inquiryDetail"    => [Array (
                        "inquiryItemId"       => $frq_detail_Info->InquirySeq,
                        "inquiryItemName"     => $frq_detail_Info->MaterialName,
                        "inquiryItemQuantity" => floor($frq_detail_Info->Quantity),
                        "inquiryItemUnit"     => $frq_detail_Info->QuantityUnitID,
                    )],
                ),
            );

            $Json_package = json_encode($Json_package);
            array_push($jsonPackage, $Json_package);

        };

      }


      // sent jsonPackage to message bus by api
      foreach ($jsonPackage as $key => $value) {

          $token = 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJmYzNoTnZwLXY1RGVFY1dCOG0zcU16S2tGaGd5ZEw1OG0tdHdodjFtdExZIn0.eyJleHAiOjE3MjU0MjkyMzAsImlhdCI6MTYzOTExNTYzMCwianRpIjoiOTQxNzZmN2UtYmE2Ny00NWMwLWE5MzEtODBjYWRmOGJmMjMwIiwiaXNzIjoiaHR0cDovLzM1LjIyMy4zOS4xOTI6ODE4MC9hdXRoL3JlYWxtcy9jbG91ZC1tYWNoaW5lcnkiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiOGFlMTFkNzgtZTgzZi00MTg4LTk2NGMtYjJiYTVlYmVhNjgzIiwidHlwIjoiQmVhcmVyIiwiYXpwIjoibG9naW4tYXBwIiwic2Vzc2lvbl9zdGF0ZSI6IjIxYzlmNDE3LTUxOTAtNDc4MC05ZGE5LTZhNDZjMmExMTRiZiIsImFjciI6IjEiLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsib2ZmbGluZV9hY2Nlc3MiLCJkZWZhdWx0LXJvbGVzLWNsb3VkLW1hY2hpbmVyeSIsInVtYV9hdXRob3JpemF0aW9uIiwiYXBwLXVzZXIiXX0sInJlc291cmNlX2FjY2VzcyI6eyJsb2dpbi1hcHAiOnsicm9sZXMiOlsidXNlciJdfSwiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJwcm9maWxlIGVtYWlsIiwic2lkIjoiMjFjOWY0MTctNTE5MC00NzgwLTlkYTktNmE0NmMyYTExNGJmIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJwcmVmZXJyZWRfdXNlcm5hbWUiOiJjb21wYW55YSJ9.OeSQsFW4s64nfvkEBj_USeOcTVwtyrHtv_nS8Z1eK2JiX2P9UWdKKAb7Vx5vpF6srlLPnjAwzIijYnxCTW-WqaVcVBNFsXnSPJ5VnzUcJHtP3eqI-f-78ukowtYc1vPmOXN8I0mB4s3j1IOGhrM5GwKPXEpjTzcx6N5os5FNEFGTa0vrFyhGGNLbZ7IlOG73zqaCcp3fxmK_fjlzg0QdpTFtZY3zJCY8pSsiZ2wGpqHj6c70qf2CUBXy5kBvq1l-kBnjsWpOU4sZD2a-HhjsC7kheSGAbShLz4WWRHauaLJLCY_RLx0qx9xsNiIxRpgZLbqRgsUiiewwvfZrcByN_w';

          $client = new \GuzzleHttp\Client([
              'base_uri' => 'http://35.223.39.192:80',
              'headers'  => [
                             'Content-Type'  => 'application/json',
                             'Authorization' => "bearer {$token}"
                            ]
          ]);

          $res = $client->request('POST', '/api/inquiry', [
                              'body' => $value,
                          ]);

          echo $res->getBody();
      };

    }
}
