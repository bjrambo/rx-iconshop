// 회원상품 추가후
function completeInsertMemberIcon(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var page = ret_obj['page'];

    alert(message);

    var url = current_url.setQuery('act','dispIconshopAdminMemberIconList');
    if(page) url = url.setQuery('page', page);

    location.href = url;
}

// 아이콘 일괄삭제
function doDeleteIcons() {
    var fo_obj = xGetElementById("icon_fo");
    var icon_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) icon_srl[icon_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) icon_srl[icon_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(icon_srl.length<1) { alert(null_message); return; }
    if(!confirm(delete_message)) return;

    var params = new Array();
    params['icon_srls'] = icon_srl.join(',');
    exec_xml('iconshop','procIconshopAdminIconDelete', params, completeDeleteIcons);
}

// 회원아이콘 일괄삭제
function doDeleteMemberIcons() {
    var fo_obj = xGetElementById("member_icon_fo");
    var data_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) data_srl[data_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) data_srl[data_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(data_srl.length<1) { alert(null_message); return; }
    if(!confirm(delete_message)) return;

    var params = new Array();
    params['data_srls'] = data_srl.join('|');
    exec_xml('iconshop','procIconshopAdminMemberIconDelete', params, completeDeleteIcons);
}

// 로그삭제
function doDeleteLogs() {
    var fo_obj = xGetElementById("log_fo");
    var data_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) data_srl[data_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) data_srl[data_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(data_srl.length<1) { alert(null_message); return; }
    if(!confirm(delete_message)) return;

    var params = new Array();
    params['data_srls'] = data_srl.join('|');
    exec_xml('iconshop','procIconshopAdminLogDelete', params, completeDeleteIcons);
}

// 기록초기화
function doResetLogs() {
    if(!confirm(reset_message)) return;
    exec_xml('iconshop','procIconshopAdminLogReset', {}, completeDeleteIcons);
}

/* 일괄 삭제 후 */
function completeDeleteIcons(ret_obj) {
    alert(ret_obj['message']);
    location.reload();
}

// 아이콘 검색 -> 선택
function iconSelected(target_id,val) {
    if(!opener.document.getElementById(target_id)) return;
    opener.document.getElementById(target_id).value = val;
    window.close();
}