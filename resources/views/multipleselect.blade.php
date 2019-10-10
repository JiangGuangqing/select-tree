<style>
    .{{$id}}_dropdown {
        margin-top: 6px;
    }

    .{{$id}}_dropdown .dropdown-toggle {
        height: 20px;
        padding: 6px 12px;
        border: 1px solid #d2d6de;
        color: #555;
        font-size: 14px;

    }
    .dropdown-menu{
        max-height:300px;
        overflow-y: auto;
    }
    .{{$id}}_dropdown .dropdown-menu > li {
        padding: 8px;
        border-bottom: 1px solid rgba(0, 0, 0, .15);
    }

    .{{$id}}_dropdown .dropdown-menu > li:last-child {
        border-bottom: 0;
    }

    .{{$id}}_dropdown .dropdown-menu > li li {
        padding: 5px;
        border-bottom: 1px dotted rgba(0, 0, 0, .15);
    }

    .{{$id}}_dropdown .dropdown-menu > li li:last-child {
        border-bottom: 0;
    }

    .{{$id}}_dropdown .dropdown-menu label {
        font-weight: 400;
    }

    .{{$id}}_dropdown .dropdown-menu ul {
        list-style: none;
    }
</style>
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <div class="dropdown {{$id}}_dropdown">
            <a class="dropdown-toggle">
                <span class="text"></span>&nbsp;&nbsp;
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu"></ul>
        </div>
        <select id="{{$id}}" name="{{$name}}[]" multiple style="display: none;"></select>
        @include('admin::form.help-block')
    </div>
</div>
<script>
    (function () {
        //修改控件文字
        var select_value = function (id, title) {
            var select = $('#{{$id}}');
            if (select.children(`option[value=${id}]`).length) {
                select.children(`option[value=${id}]`).remove();
                $('.{{$id}}_dropdown .dropdown-toggle .text').text(function (i, o) {
                    o = '|' + o + '|';
                    o = o.replace(`|${id}:${title}|`, '|').replace(`|${id}:${id}|`, '|');
                    return o.replace(/^\||\|$/g, '');
                })
            } else {
                $('#{{$id}}').append(`<option selected value="${id}">${title}</option>`);
                $('.{{$id}}_dropdown .dropdown-toggle .text').text(function (i, o) {
                    return `${o ? o + '|' : ''}${id}:${title}`;
                })
            }
        };
        //增加列表项
        var addSelect = function (parent_id, dom) {
            $.get("{{$vars['url']}}", {q: parent_id}, function (data) {
                console.log(data);
                var checkbox =  '' ;
                data.forEach(function(v){
                    checkbox += `<li>&emsp;<input type="checkbox" name="{{$name}}" class="{{$name}}_checkbox" ${Object.values(checkbox_value).includes(v.id) ? 'checked' : ''}  value="${v.id}" ><\/input><label>&emsp;<span class="title">${v.title}<\/span>&emsp;<span class="caret"><\/span><\/label>`;
                    checkbox +=  '<ul style="display: none;">' ;
                    v.list.forEach(function(vv){
                        checkbox += `<li>&emsp;<input type="checkbox" name="{{$name}}" class="{{$name}}_checkbox" ${Object.values(checkbox_value).includes(vv.id) ? 'checked' : ''} value="${vv.id}" ><\/input><label>&emsp;<span class="title">${vv.title}<\/span>&emsp;<span class="caret"><\/span><\/label>`;
                        checkbox +=  '<ul style="display: none;">' ;
                        vv.list.forEach(function(vvv){
                            checkbox += `<li>&emsp;<input type="checkbox" name="{{$name}}" class="{{$name}}_checkbox" ${Object.values(checkbox_value).includes(vvv.id) ? 'checked' : ''} value="${vvv.id}" ><\/input><label>&emsp;<span class="title">${vvv.title}<\/span>&emsp;<span class="caret"><\/span><\/label>`;
                            checkbox +=  '<ul style="display: none;">' ;
                            vvv.list.forEach(function(vvvv){
                                checkbox += `<li>&emsp;<input type="checkbox" name="{{$name}}" class="{{$name}}_checkbox" ${Object.values(checkbox_value).includes(vvvv.id) ? 'checked' : ''} value="${vvvv.id}" ><\/input><label>&emsp;<span class="title">${vvvv.title}<\/span>&emsp;<span class="caret"><\/span><\/label>`;
                                checkbox += '<\/ul>' ;
                                checkbox += `<\/li>`;
                            });
                            checkbox += '<\/ul>' ;
                            checkbox += '<\/ul>' ;
                            checkbox += `<\/li>`;
                        });
                        checkbox += '<\/ul>' ;
                        checkbox += '<\/ul>' ;
                        checkbox += `<\/li>`;
                    });
                    checkbox += '<\/ul>' ;
                    checkbox += `<\/li>`;
                });
                console.log(checkbox);
                dom.append(checkbox);
            });
        };

        var strValue='{{$value}}';
        var checkbox_value=[];
        var arrValue=[];
        if(strValue) {
            arrValue = strValue.split('|');
            for (var i = 0; i < arrValue.length; i++) {
                checkbox_value += arrValue[i].split(',')[0];
                select_value(arrValue[i].split(',')[0], arrValue[i].split(',')[1]);
                if (i < arrValue.length - 1) {
                    checkbox_value += ',';
                }
            }
            //转整型数组
            checkbox_value = checkbox_value.split(",");
            checkbox_value.forEach(item => {
                checkbox_value.push(+item);
            });
        }
        addSelect({{$vars['top_id']}}, $('ul.dropdown-menu'));

        //监听器
        //父级栏目展开按钮点击触发，控制子栏目显示隐藏
        $('.{{$id}}_dropdown .dropdown-menu ').on('click', 'li label', function (e) {
            var li = $(this).parent()
            if (li.children('ul').length) {
                li.children('ul').toggle();
            }
            e.stopPropagation();   //阻止点击列表时冒泡
        });
        //复选框被选中时触发
        $('.{{$id}}_dropdown .dropdown-menu').on('click','li input:checkbox', function (e) {
            var that = $(this);
            select_value(that.val(), that.next().find('.title').text());
            //选中状态向上遍历，否则跳过
            if(that.prop('checked')){
                that.parents().prevAll('input:checkbox').each(function(index){
                    if( !$(this).prop('checked')){
                        $(this).prop('checked','checked');
                        select_value($(this).val(), $(this).next().find('.title').text());
                    }
                });
            }else{
                //取消选择向下遍历，取消所有勾选
                console.log(that.nextAll().find('input:checkbox').length);
                that.nextAll().find('input:checkbox').each(function(index){
                    if( $(this).prop('checked')){
                        $(this).removeAttr('checked');
                        select_value($(this).val(), $(this).next().find('.title').text());
                    }
                });
            }
        });
        //点击下拉列表控件触发，切换列表显示隐藏
        $('.{{$id}}_dropdown a.dropdown-toggle').on('click', function (e) {
            $(this).parent().toggleClass('open');
            e.stopPropagation();   //阻止点击列表时冒泡
        });
        //点击空白处收起列表
        $('.content').on('click', function (e) {
            $('.{{$id}}_dropdown').removeClass('open');
        });
        //点击列表面板时触发
        $('.{{$id}}_dropdown .dropdown-menu').on('click', function (e) {
            e.stopPropagation();   //阻止点击列表时冒泡
        });

    }())
</script>