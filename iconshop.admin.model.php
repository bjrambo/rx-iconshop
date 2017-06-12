<?php
    /**
     * @class  iconshopAdminModel
     * @author 러키군 (admin@barch.kr)
     * @brief  iconshop 모듈의 AdminModel class
     **/

    class iconshopAdminModel extends module {
        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 상품리스트 구해옴
         **/
        function getIconList($list_count=20,$page_count=10) {
            // 검색/정렬옵션
            $search_keyword = trim(Context::get('search_keyword'));
            $search_target = Context::get('search_target');
            $search_target_list = array("s_title","s_content","icon_srl","s_total_count","s_total_count_less","s_total_count_more","s_price","s_price_less","s_price_more","s_minute","s_minute_less","s_minute_more","s_level_limit");
            $search_limit = Context::gets('buy_limit','send_limit','sell_limit','point_limit','event_limit','minute_limit');
            $args = null;
            if(in_array($search_limit->buy_limit,array("Y","N"))) $args->buy_limit = $search_limit->buy_limit;
            if(in_array($search_limit->send_limit,array("Y","N"))) $args->send_limit = $search_limit->send_limit;
            if(in_array($search_limit->sell_limit,array("Y","N"))) $args->sell_limit = $search_limit->sell_limit;
            if(in_array($search_limit->event_limit,array("Y","N"))) $args->event_limit = $search_limit->event_limit;
            if(in_array($search_limit->minute_limit,array("Y","N"))) $args->minute_limit = $search_limit->minute_limit;
            if($search_keyword && in_array($search_target,$search_target_list)) {
                if(array_search($search_target,$search_target_list) > 1) $args->{$search_target} = (int)$search_keyword;
                else $args->{$search_target} = $search_keyword;
            }
            $args->sort_index = "icon_srl";
            $args->sort_order = "desc";
            // 기타 변수들 정리
            $args->page = Context::get('page');
            $args->list_count = (int)$list_count;
            $args->page_count = (int)$page_count;

            $output = executeQuery("iconshop.getIconList",$args);
            return $output;
        }
        /**
         * @brief 회원의 상품목록 구해옴
         **/
        function getMemberIconList() {
            $search_keyword = trim(Context::get('search_keyword'));
            $search_target = Context::get('search_target');
            $search_target_list = array("s_data_srl","s_icon_srl","s_member_srl","s_start_date","s_start_date_less","s_start_date_more","s_end_date","s_end_date_less","s_end_date_more","s_user_id","s_nick_name","s_ipaddress");
            $args = null;
            if($search_keyword && in_array($search_target,$search_target_list)) {
                if(array_search($search_target,$search_target_list) < 5) $args->{$search_target} = (int)$search_keyword;
                else $args->{$search_target} = $search_keyword;
            }
            $args->sort_index = "data_srl";
            $args->sort_order = "desc";
            // 기타 변수들 정리
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;

            $output = executeQuery("iconshop.getMemberIconList",$args);
            return $output;
        }

        /**
         * @brief 로그리스트 구해옴
         **/
        function getLogList() {
            $search_keyword = trim(Context::get('search_keyword'));
            $search_target = Context::get('search_target');
            $search_target_list = array("s_data_srl","s_icon_srl","s_sender_srl","s_receive_srl","s_point","s_content","s_ipaddress");
            $s_category_srl = Context::get('s_category_srl');
            $s_regdate_less = Context::get('s_regdate_less');
            $s_regdate_more = Context::get('s_regdate_more');

            $args = null;
            if($s_category_srl) $args->s_category_srl = (int)$s_category_srl;
            if($s_regdate_less) $args->s_regdate_less = (int)$s_regdate_less;
            if($s_regdate_more) $args->s_regdate_more = (int)$s_regdate_more;
            if($search_keyword && in_array($search_target,$search_target_list)) {
                if(array_search($search_target,$search_target_list) < 4) $args->{$search_target} = (int)$search_keyword;
                else $args->{$search_target} = $search_keyword;
            }
            $args->sort_index = "regdate";
            $args->sort_order = "desc";
            // 기타 변수들 정리
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $output = executeQuery("iconshop.getLogList",$args);
            return $output;
        }
    }
?>
