function iconBuy(icon_srl) {
    if(!icon_srl) return;
    if(!confirm(confirm_buy)) return;

    var params = new Array();
    params['icon_srl'] = icon_srl;
    params['is_selected'] = (confirm(confirm_icon_change))? "Y" : "N";
    exec_xml('iconshop', 'procIconshopIconBuy', params, completeReload);
}

function iconSelected(data_srl) {
    if(!data_srl) return;
    if(!confirm(confirm_isselected)) return;

    var params = new Array();
    params['data_srl'] = data_srl;
    exec_xml('iconshop', 'procIconshopIconSelected', params, completeReload);
}

function iconSell(data_srl) {
    if(!data_srl) return;
    if(!confirm(confirm_sell)) return;

    var params = new Array();
    params['data_srl'] = data_srl;
    exec_xml('iconshop', 'procIconshopIconSell', params, completeReload);
}

function iconDelete(data_srl) {
    if(!data_srl) return;
    if(!confirm(confirm_delete)) return;

    var params = new Array();
    params['data_srl'] = data_srl;
    exec_xml('iconshop', 'procIconshopIconDelete', params, completeReload);
}

function completeReload(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    alert(message);

    location.reload();
}

function completeClose(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    alert(message);

    window.close();
}

// 회원 검색 -> 선택
function memberSelected(target_id,val) {
    if(!opener.document.getElementById(target_id)) return;
    opener.document.getElementById(target_id).value = val;
    window.close();
}

function send_message_chk(val) {
        if(!document.getElementById('content')) return;
        var content = document.getElementById('content');
        if(val === true)  content.disabled = "";
        else  content.disabled = "true";
}