<?php
    /**
     * @file   ko.lang.php
     * @author 러키군 (admin@barch.kr)
     * @brief  아이콘샵 모듈의 기본 언어팩
     **/
    $lang->iconshop = "아이콘샵";

    // 아바타샵설정 -> 항목별 한글이름
    $lang->icon_size = "아이콘 크기";
    $lang->icon_max_width = "가로 길이 제한";
    $lang->icon_max_height = "세로 길이 제한";
    $lang->send_fee = "선물시 수수료";
    $lang->fee = "수수료";
    $lang->sell_per = "되팔때 돌려줄 금액";
    $lang->log_save_day = "로그 보관일수";
    $lang->new_hour = "new 출력 시간";
    $lang->member_max_count = "최대 보유갯수";
    $lang->item_delete_event = "기간만료시 쪽지발송";
    $lang->list_count = "출력갯수";
    $lang->cols_list_count = "가로 출력갯수";
    $lang->member_info_print = "회원정보 출력";

    // 공통으로 사용되는 변수    
    $lang->iconshop_checked_list = array("Y","N");
    $lang->iconshop_checked_list2 = array("N","Y");

    //  메뉴별 이름
    $lang->skin_config = "스킨 설정";
    $lang->iconList = "상품목록";
    $lang->iconInsert = "상품추가";
    $lang->iconInfo = "상품정보";
    $lang->iconModify = "상품정보 수정";
    $lang->memberIconList = "회원상품 관리";
    $lang->memberIconInsert = "회원상품 추가";
    $lang->memberIconModify = "회원상품 수정";
    $lang->iconshopLogList = "로그관리";
    $lang->iconSearch = "상품검색";
    $lang->memberSearch = "회원검색";
    $lang->iconTrace = "상품추적";
    $lang->logTrace = "로그추적";
    $lang->iconshop_main_menu = array(
        "shop" => array("title" => "상점","act" => "dispIconshopIndex"),
        "my_icon" => array("title" => "내 보관함","act" => "dispIconshopMyIcon")
    );

    // 변수별 한글이름
    $lang->icon_srl = "상품번호";
    $lang->data_srl = "고유번호";
    $lang->member_srl = "회원번호";
    $lang->icon_title = "상품이름";
    $lang->icon_total_count = "상품수량";
    $lang->icon_price = "상품가격";
    $lang->icon_file1 = "첨부파일";
    $lang->icon_minute = "남은시간";
    $lang->count_infinite = "무제한";
    $lang->icon_buy = "구입";
    $lang->icon_send = "선물";
    $lang->icon_sell = "판매";
    $lang->icon_delete = "삭제";
    $lang->is_selected = "대표설정";
    $lang->sender = "보낸이";
    $lang->receiver = "받는이";
    $lang->point = "포인트";
    $lang->level = "레벨";
    $lang->unit_count = "개";
    $lang->sellout_count = "품절";
    $lang->send_message = "쪽지 발송";
    $lang->buy_limit = "구입제한";
    $lang->send_limit = "선물제한";
    $lang->sell_limit = "판매제한";
    $lang->point_limit = "포인트차감";
    $lang->minute_limit = "시간제한";
    $lang->event_limit = "이벤트제한";
    $lang->level_limit = "레벨제한";
    $lang->group_limit = "그룹제한";
    $lang->start_date = "시작일";
    $lang->end_date = "만료일";

    /**
     ** 관리자페이지 -> 목록별 검색대상
     */
     // 상품목록
    $lang->iconshop_search_target = array(
        "s_title" => "상품이름",
        "s_content" => "설명",
        "s_icon_srl" => "상품번호",
        "s_total_count" => "상품수량",
        "s_total_count_more" => "상품수량 (이상)",
        "s_total_count_less" => "상품수량 (이하)",
        "s_price" => "상품 가격",
        "s_price_more" => "상품 가격(이상)",
        "s_price_less" => "상품 가격(이하)",
        "s_minute" => "시간",
        "s_minute_more" => "시간(이상)",
        "s_minute_less" => "시간(이하)",
        "s_level_limit" => "레벨제한"
    );
    // 회원상품목록
    $lang->iconshop_search_target2 = array(
        "s_data_srl" => "고유번호",
        "s_icon_srl" => "상품번호",
        "s_member_srl" => "회원번호",
        "s_user_id" => "회원ID",
        "s_nick_name" => "회원닉네임",
        "s_start_date" => "시작일",
        "s_start_date_more" => "시작일(이상)",
        "s_start_date_less" => "시작일(이하)",
        "s_end_date" => "만료일",
        "s_end_date_more" => "만료일(이상)",
        "s_end_date_less" => "만료일(이하)",
        "s_ipaddress" => "IP"
    );
    // 로그관리
    $lang->iconshop_search_target3 = array(
        "s_data_srl" => "고유번호",
        "s_icon_srl" => "상품번호",
        "s_sender_srl" => "보낸이",
        "s_receive_srl" => "받는이",
        "s_point" => "포인트",
        "s_content" => "내용",
        "s_ipaddress" => "IP"
    );
    $lang->iconshop_log_act_list = array(
        1 => "구입",
        2 => "선물",
        3 => "판매",
        4 => "삭제"
    );

    $lang->null_title = "상품이름을 입력 해주세요.";
    $lang->null_file1 = "첨부파일을 업로드 해주세요.";
    $lang->invalid_icon = "존재하지 않는 상품입니다.";
    $lang->invalid_receive = "받는이가 존재하지 않습니다.";
    $lang->invalid_image_format = "첨부파일은 gif,jpg,jpeg,png 파일만 업로드 가능합니다.";
    $lang->invalid_member = "존재하지 않는 사용자입니다.";
    $lang->invalid_module = "존재하지 않는 모듈입니다.";

    // 항목별 부가설명
    $lang->icon_size_about = "첨부파일의 이미지크기가 제한된 길이보다 클경우 리사이즈 됩니다.";
    $lang->send_fee_about = "아이콘을 선물할때 수수료를 설정할수 있습니다. (0~100%)";
    $lang->sell_per_about = "되팔때 돌려줄 금액을 설정할수 있습니다. (0~100%)";
    $lang->log_save_day_about = "데이터가 많이 쌓이는걸 방지 하기위해 보관일이 지난로그는 삭제됩니다.<br />0 입력시 삭제되지 않습니다.";
    $lang->new_hour_about = "상점에서 new아이콘을 출력할 시간을 설정할수 있습니다.<br />0 입력시 출력되지 않습니다.";
    $lang->member_max_count_about = "회원이 보유할수있는 아이콘의 갯수를 설정할수 있습니다.<br />0 입력시 제한이 없습니다.";
    $lang->item_delete_event_about = "시간제 아이콘이 기간만료로 삭제될경우 쪽지를 발송합니다. ";
    $lang->sender_config_about = "쪽지발송시 보낸이를 설정할수 있습니다.<br />설정하지 않으면 보낸이가 자신으로 설정됩니다.";
    $lang->iconshop_replace_about = "# 치환자 리스트<br /><br />[member_srl] -> 회원번호<br />[nick_name] -> 회원닉네임<br />[icon_srl] -> 상품번호<br />[icon_title] -> 상품이름<br />[start_date] - 구입시간 ex) 2010/06/19 20:08<br />[end_date] - 만료시간 ex) 2010/06/19 20:38";
    $lang->total_count_about = "판매할 수량을 입력하세요.<br />-1을 입력하시면 수량제한이 없습니다.";
    $lang->end_date_about = "만료일을 임의로 지정할수 있습니다. (지정하지 않으면 상품의 시간제한으로 대체)";
    $lang->buy_limit_about = "N을 선택하면 이 상품을 구입할수 없습니다.";
    $lang->send_limit_about = "N을 선택하면 이 상품을 선물할수 없습니다.";
    $lang->sell_limit_about = "N을 선택하면 이 상품을 되팔수 없습니다.";
    $lang->point_limit_about = "N을 선택하면 포인트 검사만 하고 차감되진 않습니다.";
    $lang->minute_limit_about = "구입시간을 기준으로, 사용할수 있는 시간을 설정할수 있습니다.<br />1일 - 1440분<br />7일 - 10080분";
    $lang->event_limit_about = "Y를 선택하면 이 상품을 특정기간에만 구입할수 있습니다.";
    $lang->level_limit_about = "이 상품을 특정레벨 이상만 구입할수 있습니다.";
    $lang->group_limit_about = "특정그룹에 속한 회원만 구입할수 있습니다.";
    $lang->iconSearch_about = "검색결과는 최대 10건까지만 출력됩니다.";
    $lang->image_format_about = "현재 설정된 아이콘 크기는 %dpx * %dpx이며, gif,jpg,jpeg,png파일만 업로드 가능합니다.";
    $lang->send_message_title = "%s님이 [%s]아이콘을 선물 보내셨습니다.";
    $lang->list_count_about = "상점/내 보관함에 출력될 리스트 갯수를 설정합니다.";

    $lang->file1_upload_error = "첨부파일 업로드에 실패했습니다. Code -1";
    $lang->max_count_error = "최대 보유갯수를 초과하였습니다.";
    $lang->buy_limit_error = "구입할수 없는 상품입니다.";
    $lang->send_target_error = "자신에게 선물할수 없습니다.";
    $lang->send_limit_error = "선물할수 없는 상품입니다.";
    $lang->sell_limit_error = "되팔수 없는 상품입니다.";
    $lang->event_limit_error = "이벤트기간에만 구입/선물할수 있는 상품입니다.";
    $lang->level_limit_error = "레벨이 낮아 구입/선물할수 없는 상품입니다.";
    $lang->group_limit_error = "구입/선물할수 있는 그룹원이 아닙니다.";
    $lang->count_limit_error = "품절된 상품입니다.";
    $lang->point_limit_error = "포인트가 부족합니다.";
    $lang->already_icon = "이미 보유하고 있는 상품입니다.";
    $lang->already_isselected = "이미 대표아이콘으로 설정되어 있습니다.";

    $lang->confirm_buy = "이 상품을 구입하시겠습니까?";
    $lang->confirm_icon_change = "구입후 대표아이콘으로 변경하시겠습니까?";
    $lang->confirm_send = "선물 보내시겠습니까?";
    $lang->confirm_sell = "이 상품을 되파시겠습니까?\\n구입금액의 %s만 돌려받을수 있습니다.";
    $lang->confirm_isselected = "대표아이콘으로 변경하시겠습니까?";

    $lang->success_buy = "상품을 구입 했습니다.";
    $lang->success_send = "상품을 선물 했습니다.";
    $lang->success_sell = "상품을 판매 하였습니다. (+%d 획득)";
    $lang->success_isselected = "대표아이콘으로 설정되었습니다.";
?>