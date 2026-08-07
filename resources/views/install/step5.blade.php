<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>{{$Title}} - {{$Powered}}</title>
    <link rel="stylesheet" href="/install/css/install.css" />
    <link rel="stylesheet" href="/install/css/step5.css" />
    <script src="/install/js/jquery.js"></script>
    @php
        $hostName = request()->getHost();
        $siteUrl = request()->getSchemeAndHttpHost();
    @endphp
</head>
<body>
<div class="wrap">
    <div class="title">
        {{ __('frontend.install.complete') }}
    </div>
    <section class="section">
        <div class="title">
            <img src="/install/images/success.png" alt="">
            <h1>{{ __('frontend.install.success') }}</h1>
        </div>
        <div class="progress">
            <div class="trip p8">
                {{ __('frontend.install.security_notice') }}
            </div>
        </div>
        <div class="bottom-btn">
{{--            <div class="pre btn">--}}
{{--                <a href="<?php echo 'http://'.$host;?>/work" class="btn mid">进入前台</a>--}}
{{--            </div>--}}
            <div class="admin btn">
                <a href="{{ $siteUrl }}/admin" class="btn btn_submit J_install_btn mid">{{ __('frontend.install.enter_admin') }}</a>
            </div>
        </div>
    </section>
</div>
@include('install/footer')
<script>
    $(function(){
        $.ajax({
            type: "POST",
            url: "http://shop.crmeb.net/index.php/admin/server.upgrade_api/updatewebinfo",
            header:{
                'Access-Control-Allow-Origin':'*',
                'Access-Control-Allow-Headers':'X-Requested-With',
                'Access-Control-Allow-Methods':'PUT,POST,GET,DELETE,OPTIONS'
            },
            data: {
                host:@json($hostName),
                https:@json($siteUrl),
                version:@json($version),
                version_code:@json($platform),
                ip:@json($ip),
                uid:@json($uid),
            },
            dataType: 'json',
            success: function(){}
        });
    });
</script>
</body>
</html>
