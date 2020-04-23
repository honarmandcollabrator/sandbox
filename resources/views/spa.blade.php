<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="/images/application/tootiko-alone.png" type="image/x-icon">
    <title>طوطیکو | سفارش ترجمه آنلاین</title>

    <ul>
        <ul class="select2-results__options" role="tree" id="select2-project-field-results" aria-expanded="true"
            aria-hidden="false">
            <li class="select2-results__option" role="treeitem" aria-dis  abled="true">انتخاب زمینه </li>
            <li class="select2-results__option" id="select2-project-field-result-fet8-100" role="treeitem"
                aria-selected="false">   عمومی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-dpmx-101" role="treeitem"
                aria-selected="false">   پزشکی
             </li>
            <li class="select2-results__option select2-results__option--highlighted"
                id="select2-project-field-result-14p4-102" role="treeitem" aria-selected="false">   زیست شناسی و علوم
                آزمایشگاهی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-tvj4-103" role="treeitem"
                aria-selected="false">   علم شیمی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-9gzd-104" role="treeitem"
                aria-selected="false">   مالی - حسابداری
             </li>
            <li class="select2-results__option" id="select2-project-field-result-ttxz-105" role="treeitem"
                aria-selected="false">   معارف اسلامی و الهیات
             </li>
            <li class="select2-results__option" id="select2-project-field-result-3abk-106" role="treeitem"
                aria-selected="false">   حقوق
             </li>
            <li class="select2-results__option" id="select2-project-field-result-v0ey-107" role="treeitem"
                aria-selected="false">   مدیریت
             </li>
            <li class="select2-results__option" id="select2-project-field-result-zh9g-108" role="treeitem"
                aria-selected="false">   مهندسی مکانیک
             </li>
            <li class="select2-results__option" id="select2-project-field-result-i7gm-109" role="treeitem"
                aria-selected="false">   مهندسی عمران
             </li>
            <li class="select2-results__option" id="select2-project-field-result-1p4e-110" role="treeitem"
                aria-selected="false">   مهندسی معماری
             </li>
            <li class="select2-results__option" id="select2-project-field-result-w10t-111" role="treeitem"
                aria-selected="false">   مهندسی کامپیوتر
             </li>
            <li class="select2-results__option" id="select2-project-field-result-0tou-112" role="treeitem"
                aria-selected="false">   مجموعه مهندسی برق
             </li>
            <li class="select2-results__option" id="select2-project-field-result-n8h6-113" role="treeitem"
                aria-selected="false">   قرارداد و اسناد تجاری
             </li>
            <li class="select2-results__option" id="select2-project-field-result-86o5-114" role="treeitem"
                aria-selected="false">   روانشناسی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-otul-115" role="treeitem"
                aria-selected="false">   ادبیات و زبان شناسی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-802g-116" role="treeitem"
                aria-selected="false">   مواد و متالوژی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-r2qx-117" role="treeitem"
                aria-selected="false">   علم فیزیک
             </li>
            <li class="select2-results__option" id="select2-project-field-result-lxok-118" role="treeitem"
                aria-selected="false">   مجموعه ریاضیات و آمار
             </li>
            <li class="select2-results__option" id="select2-project-field-result-44qc-119" role="treeitem"
                aria-selected="false">   گردشگری
             </li>
            <li class="select2-results__option" id="select2-project-field-result-o5h2-120" role="treeitem"
                aria-selected="false">   کشاورزی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-fesz-121" role="treeitem"
                aria-selected="false">   اقتصاد
             </li>
            <li class="select2-results__option" id="select2-project-field-result-ef0t-122" role="treeitem"
                aria-selected="false">   تاریخ
             </li>
            <li class="select2-results__option" id="select2-project-field-result-yluh-123" role="treeitem"
                aria-selected="false">   خبر
             </li>
            <li class="select2-results__option" id="select2-project-field-result-92jy-124" role="treeitem"
                aria-selected="false">   رزومه و انگیزه نامه
             </li>
            <li class="select2-results__option" id="select2-project-field-result-gjw9-125" role="treeitem"
                aria-selected="false">   داستان و رمان
             </li>
            <li class="select2-results__option" id="select2-project-field-result-ql3r-126" role="treeitem"
                aria-selected="false">   زمین شناسی و معدن
             </li>
            <li class="select2-results__option" id="select2-project-field-result-u3fd-127" role="treeitem"
                aria-selected="false">   سیاسی و روابط بین الملل
             </li>
            <li class="select2-results__option" id="select2-project-field-result-7f9w-128" role="treeitem"
                aria-selected="false">   صنایع غذایی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-o644-129" role="treeitem"
                aria-selected="false">   جامعه شناسی و علوم اجتماعی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-dawh-130" role="treeitem"
                aria-selected="false">   فلسفه
             </li>
            <li class="select2-results__option" id="select2-project-field-result-gsfo-131" role="treeitem"
                aria-selected="false">   اینترنت و تکنولوژی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-9tpi-132" role="treeitem"
                aria-selected="false">   محیط زیست و منابع طبیعی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-1d2k-133" role="treeitem"
                aria-selected="false">   مهندسی پزشکی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-lxze-134" role="treeitem"
                aria-selected="false">   نظامی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-8423-135" role="treeitem"
                aria-selected="false">   نفت، گاز و پتروشیمی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-soy1-136" role="treeitem"
                aria-selected="false">   ورزشی
             </li>
            <li class="select2-results__option" id="select2-project-field-result-8mfj-137" role="treeitem"
                aria-selected="false">   هوافضا
             </li>
            <li class="select2-results__option" id="select2-project-field-result-h2rm-138" role="treeitem"
                aria-selected="false">   مهندسی صنایع
             </li>
            <li class="select2-results__option" id="select2-project-field-result-dffy-139" role="treeitem"
                aria-selected="false">   هنر
             </li>
        </ul>
    </ul>
</head>
<body>

<div id="app"></div>

<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
