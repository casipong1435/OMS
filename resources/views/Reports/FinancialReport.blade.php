<?php

 function getPaymentCycle($cycle){
    switch($cycle){
        case 0:
            return 'Monthly';
        case 1:
            return 'Quarterly';
        case 2:
            return 'Bi-Annual';
        case 3:
            return 'Annually';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Financial Report</title>

    <style>
        body{
            margin-top: 5rem;
        }
        .header,
        .footer {
            width: 100%;
            position: fixed;
        }
        .header {
            top: 0px;
        }
        .img-left{
            position: absolute;
            top: 10;
            left:10;
        }
        .img-right{
            position: absolute;
            top: 10;
            right:20;
        }
        .header-content{
            text-align: center;
        }
        .tcgc{
            font-weight: bold;
        }
        .accession{
            color: red;
        }
        .title{
            margin-top: 5rem;
            margin-bottom: 2rem;
            font-size: 35px;
            font-weight: bold;
            text-align: center;
        }
        table{
            border-collapse: collapse;
            width: 100%;
            
        }
        th, td, tr{
            border: 1px solid black;
        }
        th,td{
            padding: 8px;
            text-align: center;
            font-family: sans-serif;
        }
        th{
            font-size: 12px;
            background: yellow;
        }
        td{
            font-size: 10px;
            color: #302f2f;
        }
    </style>
</head>
@foreach ($areas as $category)
<body>
    
    <div class="header">
        <div class="header-content">
            <div class="img-left">
                <img src="images/tangubcity.png" class="bg" width="110" height="110">
            </div>
            <div class="center">
                <div class="tcgc" style="font-size: 20px">Republic of the Philippines</div>
                <div class="lrc" style="font-size: 20px">CITY OF TANGUB</div>
                <div class="maloro" style="font-size: 20px"><i>God-Centered CIty</i></div>
                <div class="tcgc" style="font-size: 20px">City Economic Enterprises Development Office</div>
                <div class="lrc" style="font-size: 20px">CEEDO</div>
            </div>
            <div class="img-right">
                <img src="images/bagongpilinas.png" class="bg" width="110" height="110">
            </div>
        </div>
        
    </div>
    
    <div class="content">
        <div class="title">Financial Report</div>
        <div style="font-size: 16px; margin-bottom: 10px">As of January 1, 2024 - December 31, 2024</div>
        <table>
            <thead>
                <tr>
                    <th colspan="9" style="font-size: 20px">{{ $category->name }}</th>
                </tr>
                <tr>
                    <th>Owner's Name</th>
                    <th>Business</th>
                    <th>Plate</th>
                    <th>Kind of Business</th>
                    <th>Barangay</th>
                    <th>Amount Paid</th>
                    <th>Payment Cycle</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead> 
            <tbody>
                @foreach ($vendors as $business)
                    @if ($business->establishment_unit->establishment->area->id == $category->id)
                    <tr>
                        <td>{{ $business->profile->first_name.' '.$business->profile->middle_name.' '.$business->profile->last_name }}</td>
                        <td>{{ $business->name }}</td>
                        <td>{{ $business->plate }}</td>
                        <td>{{ $business->kind_of_business }}</td>
                        <td>{{ $business->profile->ownBarangay->brgyDesc }}</td>
                        <td>{{ $business->total_payment }}</td>
                        <td>{{ getPaymentCycle($business->payment_cycle)  }}</td>
                        <td>{{ $business->status == 1 ? 'Active' : 'Closed' }}</td>
                        <td>{{ $business->remarks }}</td>
                    </tr>
                    @endif
                    
                @endforeach
                @if (count($vendors) <= 0)
                    <tr>
                        <td colspan="9">No Data!</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
</body>
@endforeach
</html>