<html lang="zh-CN">
<head>
    <title>{{ __('frontend.documents.title') }}</title>
</head>
<style>
    iframe {
        height: 500px;
    }
</style>
<link rel='stylesheet' type='text/css' href='https://www.layuicdn.com/layui/css/layui.css'/>
<body>
<h2>{{ __('frontend.documents.title') }}</h2>
<div style='margin: 20px'>
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>{{ __('frontend.documents.route_list') }}</legend>
    </fieldset>
    <button type="button" onclick="news()" class="layui-btn layui-btn-sm">{{ __('frontend.documents.new') }}</button>
    <div class="layui-form">
        <table class="layui-table">
            <thead>
            <tr>
                <th>{{ __('frontend.documents.file_name') }}</th>
                <th>{{ __('frontend.documents.file_id') }}</th>
                <th>{{ __('frontend.documents.creator') }}</th>
                <th>{{ __('frontend.documents.actions') }}</th>
            </tr>
            @foreach($data['list'] as $item)
                <tr>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['file_id']}}</td>
                    <td>{{$item['uid']}}</td>
                    <td>
                        <button type="button" class="layui-btn layui-btn-sm" onclick="view({{$item['file_id']}})">{{ __('frontend.documents.view') }}
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm" onclick="edit({{$item['file_id']}})">{{ __('frontend.documents.edit') }}
                        </button>
                    </td>
                </tr>
            @endforeach
            </thead>
        </table>
    </div>
</div>
</body>

<script type="text/javascript">
    function edit(file_id) {
        window.open('/api/ent/edit/' + file_id);
    }

    function view(file_id) {
        window.open('/api/ent/view/' + file_id);
    }

    function news() {
        window.open('/api/ent/new/');
    }

</script>
</html>
