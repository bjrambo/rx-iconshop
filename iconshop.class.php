<?php
    /**
     * @class  iconshop
     * @author 러키군 (admin@barch.kr)
     * @brief  iconshop 모듈의 high class
     **/

    class iconshop extends ModuleObject {

        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oAddonAdminModel = &getAdminModel('addon');
            $oAddonAdminController = &getAdminController('addon');

            // iconshop 모듈에서 사용할 디렉토리 생성
            FileHandler::makeDir('./files/iconshop');

            // iconshop 모듈 생성
            $iconshop_info = $oModuleModel->getModuleInfoByMid('iconshop');
            if(!$iconshop_info->module_srl) {
                $args = null;
                $args->mid = 'iconshop';
                $args->module = 'iconshop';
                $args->browser_title = '아이콘샵';
                $args->site_srl = 0;
                $args->skin = 'default';
                $oModuleController->insertModule($args);
            }

            // 아이콘샵 모듈의 기본설정 저장
            $config = null;
            $config->icon_width = 20;
            $config->icon_height = 20;
            $config->send_fee = 0;
            $config->sell_per = 50;
            $config->log_save_day = 7;
            $config->new_hour = 24;
            $config->member_max_count = 100;
            $config->list_count = 10;
            $config->cols_list_count = 2;
            $config->member_info_print = "Y";
            $config->item_delete_event = "N";
            $config->item_delete_title = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료 되었습니다.";
            $config->item_delete_message = "[nick_name]님이 구입하신 [[icon_title]] 아이콘의 시간이 만료되어 삭제 되었습니다.\n\n[[end_date]]";
            $oModuleController->insertModuleConfig('iconshop', $config);

            // 회원 모듈 설정에서 이미지 마크 사용시...
            $member_config = $oModuleModel->getModuleConfig('member');
            if($member_config->image_mark == 'Y') {
                $member_config->image_mark = "N";
                $oModuleController->insertModuleConfig('member', $member_config);
            }

            // 대표아이콘 출력 애드온 활성화 시키기
            $site_module_info = Context::get('site_module_info');
            if($oAddonAdminModel->isActivatedAddon('icon_print', $site_module_info->site_srl)) $oAddonAdminController->doDeactivate('icon_print', $site_module_info->site_srl);
            $oAddonAdminController->doActivate('member_icon_print', $site_module_info->site_srl);
            $oAddonAdminController->makeCacheFile($site_module_info->site_srl);

            return new Object();
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oAddonAdminModel = &getAdminModel('addon');

            // 2010. 06. 25 point_limit 추가
            if(!$oDB->isColumnExists("iconshop_admin","point_limit")) return true;

            // iconshop 모듈에서 사용할 디렉토리가 없으면
            if(!is_dir('./files/iconshop')) return true;

            // iconshop 모듈이 존재하지 않으면...
            $iconshop_info = $oModuleModel->getModuleInfoByMid('iconshop');
            if(!$iconshop_info->module_srl) return true;

            // iconshop 모듈의 기본설정이 없으면...
            $iconshop_config = $oModuleModel->getModuleConfig('iconshop');
            if(!$iconshop_config) return true;

            // 회원 모듈 설정에서 이미지 마크 사용시...
            $member_config = $oModuleModel->getModuleConfig('member');
            if($member_config->image_mark == 'Y') return true;

            // 대표아이콘 출력 애드온이 비활성화 되있으면..
            $site_module_info = Context::get('site_module_info');
            if($oAddonAdminModel->isActivatedAddon('icon_print', $site_module_info->site_srl)) return true;
            if(!$oAddonAdminModel->isActivatedAddon('member_icon_print', $site_module_info->site_srl)) return true;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oAddonAdminModel = &getAdminModel('addon');
            $oAddonAdminController = &getAdminController('addon');

            /**
             * 2010. 06. 25 point_limit 추가
             **/
            if(!$oDB->isColumnExists("iconshop_admin","point_limit")) {
                $oDB->addColumn('iconshop_admin',"point_limit","char",1,"Y","notnull");
            }

            // iconshop 모듈에서 사용할 디렉토리 생성
            if(!is_dir('./files/iconshop')) FileHandler::makeDir('./files/iconshop');

            // iconshop 모듈 생성
            $iconshop_info = $oModuleModel->getModuleInfoByMid('iconshop');
            if(!$iconshop_info->module_srl) {
                $args = null;
                $args->mid = 'iconshop';
                $args->module = 'iconshop';
                $args->browser_title = '아이콘샵';
                $args->site_srl = 0;
                $args->skin = 'default';
                $oModuleController->insertModule($args);
            }

            // 아이콘샵 모듈의 기본설정 저장
            $iconshop_config = $oModuleModel->getModuleConfig('iconshop');
            if(!$iconshop_config) {
                $config = null;
                $config->icon_width = 16;
                $config->icon_height = 16;
                $config->send_fee = 0;
                $config->sell_per = 50;
                $config->log_save_day = 7;
                $config->new_hour = 24;
                $config->list_count = 10;
                $config->cols_list_count = 2;
                $config->member_info_print = "Y";
                $config->member_max_count = 100;
                $config->item_delete_event = "Y";
                $config->item_delete_title = "[nick_name]님이 구입하신 '[icon_title]' 아이콘이 삭제 되었습니다.";
                $config->item_delete_message = "[nick_name]님이 구입하신 '[icon_title]' 아이콘의 기간이 만료되어 삭제 되었습니다.";
                $oModuleController->insertModuleConfig('iconshop', $config);
            }

            // 회원 모듈 설정에서 이미지 마크 사용시...
            $member_config = $oModuleModel->getModuleConfig('member');
            if($member_config->image_mark == 'Y') {
                $member_config->image_mark = "N";
                $oModuleController->insertModuleConfig('member', $member_config);
            }

            // 대표아이콘 출력 애드온이 비활성화 되있으면..
            $site_module_info = Context::get('site_module_info');
            if($oAddonAdminModel->isActivatedAddon('icon_print', $site_module_info->site_srl)) $oAddonAdminController->doDeactivate('icon_print', $site_module_info->site_srl);
            $oAddonAdminController->doActivate('member_icon_print', $site_module_info->site_srl);
            $oAddonAdminController->makeCacheFile($site_module_info->site_srl);

            return new Object(0, 'success_updated');
        }

		function moduleUninstall() {
            // iconshop 모듈에서 사용한 디렉토리 삭제
            FileHandler::removeDir(_XE_PATH_.'files/iconshop');

            return new Object();
		}

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
        }

    }
?>
