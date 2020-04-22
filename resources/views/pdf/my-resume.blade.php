<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title>رزومه کاری</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>
        .text-right {
            text-align: right;
        }

        body {
            font-family: 'hse', sans-serif;
        }


        /*@font-face {*/
        /*    font-family: hse;*/
        /*    src: url('/fonts/IRANSansWeb.woff');*/
        /*}*/

        /** {*/
        /*    font-family: yekan, serif !important;*/
        /*    letter-spacing: 0;*/
        /*}*/

    </style>



</head>
<body style="">

<div class="row">
    <div class="col-xs-5">
        <img width="75" src="/images/hse-logo.png" alt="logo">
    </div>

    <div class="col-xs-5 text-right">
        HSE JOB
    </div>

</div>
<h4 class="text-center" style="margin: 0; padding: 0">رزومه کاری</h4>
<hr>
<div class="row">
    <div class="col-xs-5">
        <img height="128" width="128" src="{{$resume->user->avatar === null ? '/images/application/default-avatar.jpg' : Storage::url($resume->user->avatar)}}">
    </div>


    {{--<script>
        document.getElementById('image').src = '/'+url;
    </script>
--}}

    <div class="col-xs-5 text-right">
        نام و نام خانوادگی:
        {{$resume->user->resume->full_name_persian}}
        <br>
        <br>
        <span dir="rtl">ایمیل:</span> <br>
        <span>
                {{$resume->user->email}}
        </span>

    </div>
    <hr>
</div>

<div>
    <style>
        table tr td {
            padding: 5px
        }
    </style>
    <h4 class="text-center">اطلاعات شخصی</h4>
    <table class="table">
        <tbody>
        <tr class="well">
            <td></td>
            <td style="width: 200px">
                <span>
                    {{$resume->national_code}}
                </span>
            </td>
            <td dir="rtl" class="text-right">
                شماره ملی:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px">
                <span>
                    {{$resume->identity_number}}
                </span>
            </td>
            <td dir="rtl" class="text-right">
                شماره شناسنامه:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->father_name}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                نام پدر:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->birthday}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                تاریخ تولد:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->is_married ? 'متاهل' : 'مجرد'}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                وضعیت تاهل:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->dependants_count}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                تعداد افراد تحت تکلف:
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h4 class="text-center">اطلاعات شغلی</h4>
    <table class="table">
        <tbody>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->jobCategory->name}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                حوزه فعالیت:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->workExperienceYears->name}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                تجربه کاری:
            </td>
            <td></td>
        </tr>
        <tr class="well">
            <td></td>
            <td style="width: 200px;padding: 5px">
                <span>
                    {{$resume->jobDegree->name}}
                </span>
            </td>
            <td style="padding: 5px" dir="rtl" class="text-right">
                مدرک تحصیلی:
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>


</div>

</body>
</html>
