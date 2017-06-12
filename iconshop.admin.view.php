<?php
    /**
     * @class  iconshopAdminView
     * @author 러키군 (admin@barch.kr)
     * @brief  iconshop 모듈의 admin view class
     **/

    class iconshopAdminView extends iconshop {

        /**
         * @brief 초기화
         *
         **/
        function init() {
            // 설정 정보 가져오기
            $oModuleModel = &getModel('module');
            $iconshop_info = $oModuleModel->getModuleInfoByMid("iconshop");
            $iconshop_config = $oModuleModel->getModuleConfig('iconshop');
            $module_category = $oModuleModel->getModuleCategories();

            // 설정 변수 지정
            Context::set('iconshop_info', $iconshop_info);
            Context::set('iconshop_config', $iconshop_config);
            Context::set('module_category', $module_category);

            // template path지정
            $this->setTemplatePath($this->module_path.'tpl');
        }

        /**
         * @brief 기본 설정
         **/
        function dispIconshopAdminConfig() {
            // 스킨목록 가져오기
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            $mskin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
            Context::set('mskin_list', $mskin_list);

            // 레이아웃 목록을 구해옴
            $oLayoutModel = &getModel('layout');
            $layout_list = $oLayoutModel->getLayoutList();
            Context::set('layout_list', $layout_list);

            $mlayout_list = $oLayoutModel->getLayoutList(0, 'M');
            Context::set('mlayout_list', $mlayout_list);

            // 템플릿 파일 지정
            $this->setTemplateFile('config');
        }

        /**
         * @brief 추가 설정
         **/
        function dispIconshopAdminAddConfig() {
            // 템플릿 파일 지정
            $this->setTemplateFile('add_config');
        }

        /**
         * @brief 스킨 설정
         **/
        function dispIconshopAdminSkinConfig() {
            // 공통 모듈 권한 설정 페이지 호출
            $oModuleAdminModel = &getAdminModel('module');
            $iconshop_info = Context::get('iconshop_info');
            $skin_content = $oModuleAdminModel->getModuleSkinHTML($iconshop_info->module_srl);
            Context::set('skin_content', $skin_content);

            // 템플릿 파일 지정
            $this->setTemplateFile('skin_config');
        }

        /**
         * @brief 상품관리
         **/
        function dispIconshopAdminIconList() {
            // 상품정보 가져오기
            $oIconshopAdminModel = &getAdminModel("iconshop");
            $icon_list = $oIconshopAdminModel->getIconList();
            Context::set('total_count', $icon_list->total_count);
            Context::set('total_page', $icon_list->total_page);
            Context::set('page', $icon_list->page);
            Context::set('icon_list',$icon_list->data);
            Context::set('page_navigation', $icon_list->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('icon_list');
        }

        /**
         * @brief 상품추가/수정 (icon_srl이 있을경우 수정)
         **/
        function dispIconshopAdminIconInsert() {
            $icon_srl = Context::get('icon_srl');

			// 등록된 그룹목록을 구해옴
			$oMemberModel = &getModel('member');
			$group_list = $oMemberModel->getGroups(0);
			Context::set('group_list',$group_list);

            // 원본 데이터 가져오기
            if($icon_srl) {
                $oIconshopModel = &getModel("iconshop");
                $icon_data = $oIconshopModel->getIconBySrl($icon_srl);
                if($icon_data) Context::set('icon_data',$icon_data);
            }

            // 템플릿 파일 지정
            $this->setTemplateFile('icon_insert');
        }

        /**
         * @brief 회원 상품관리
         **/
        function dispIconshopAdminMemberIconList() {
            // 회원 상품정보 가져오기
            $oIconshopAdminModel = &getAdminModel("iconshop");
            $icon_list = $oIconshopAdminModel->getMemberIconList();
            Context::set('total_count', $icon_list->total_count);
            Context::set('total_page', $icon_list->total_page);
            Context::set('page', $icon_list->page);
            Context::set('icon_list',$icon_list->data);
            Context::set('page_navigation', $icon_list->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('member_icon_list');
        }

        /**
         * @brief 회원상품 추가/수정
         **/
        function dispIconshopAdminMemberIconInsert() {
            $data_srl = Context::get('data_srl');

            // 수정모드일때 원본데이터 가져오기
            if($data_srl) {
                $oIconshopModel = &getModel("iconshop");
                $member_info = $oIconshopModel->getMemberIconByDataSrl($data_srl);
                if($member_info) {
                    $icon_data = $oIconshopModel->getIconBySrl($member_info->icon_srl);
                    Context::set('member_icon_data',$member_info);
                    Context::set('icon_data',$icon_data);
                }
            }

            // 템플릿 파일 지정
            $this->setTemplateFile('member_icon_insert');
        }

        /**
         * @brief 로그관리
         **/
        function dispIconshopAdminLogList() {
            // 설정 정보 가져오기
            $oModuleModel = &getModel('module');
            $iconshop_config = $oModuleModel->getModuleConfig('iconshop');

            // 보관일수가 지난 기록은 삭제..
            if($iconshop_config->log_save_day) {
                $args = null;
                $args->regdate_less = date("YmdHis",strtotime(sprintf('-%d day',$iconshop_config->log_save_day)));
                $oIconshopController = &getController('iconshop');
                $oIconshopController->DeleteLog($args);
            }

            // 상품정보 가져오기
            $oIconshopAdminModel = &getAdminModel("iconshop");
            $log_list = $oIconshopAdminModel->getLogList();
            Context::set('total_count', $log_list->total_count);
            Context::set('total_page', $log_list->total_page);
            Context::set('page', $log_list->page);
            Context::set('log_list',$log_list->data);
            Context::set('page_navigation', $log_list->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('log_list');
        }

        /**
         * @brief 아이콘검색
         **/
        function dispIconshopAdminIconSearch() {
            $params = Context::gets('search_target','search_keyword','target_id');
            switch ($params->search_target) {
                case "s_icon_srl":
                    $params->search_keyword = (int)$params->search_keyword;
                    break;
                case "s_title":
                    $params->search_keyword = $params->search_keyword;
                    break;
                default:
                    unset($params->search_keyword);
            }

            // 키워드가 있을경우 아이콘 정보 가져옴
            if($params->search_target && $params->search_keyword) {
                $args = null;
                $args->{$params->search_target} = $params->search_keyword;
                $args->page = Context::get('page');
                $args->list_count = 10;
                $args->page_count = 1;
                $args->sort_index = "icon_srl";
                $args->sort_order = "desc";

                $result_list = executeQuery("iconshop.getIconList",$args);
                Context::set('result_list',$result_list->data);
            }

            // 레이아웃을 팝업으로 지정
           $this->setLayoutFile('popup_layout');

            // 템플릿 파일 지정
            $this->setTemplateFile('icon_search');
        }

    }
?>
