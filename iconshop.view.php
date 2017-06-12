<?php

/**
 * @class  iconshopView
 * @author 러키군 (admin@barch.kr)
 * @brief  iconshop 모듈의 View class
 **/
class iconshopView extends iconshop
{

	/**
	 * @brief 초기화
	 * board 모듈은 일반 사용과 관리자용으로 나누어진다.\n
	 **/
	function init()
	{
		$oModuleModel = getModel('module');

		// 설정 정보 가져오기
		$iconshop_info = $oModuleModel->getModuleInfoByMid('iconshop');
		$iconshop_config = $oModuleModel->getModuleConfig('iconshop');
		$colorset = $oModuleModel->getModuleSkinVars($iconshop_info->module_srl);
		// 설정 변수 지정
		Context::set('iconshop_info', $iconshop_info);
		Context::set('iconshop_config', $iconshop_config);
		Context::set('cols_list_count', $iconshop_config->cols_list_count);
		Context::set('colorset', $colorset['colorset']->value);
		Context::set('iconshop_main_menu', Context::getLang('iconshop_main_menu'));

		// template path지정
		$template_path = sprintf("%sskins/%s/", $this->module_path, $iconshop_info->skin);
		if(!is_dir($template_path) || !$iconshop_info->skin)
		{
			$iconshop_info->skin = 'default';
			$template_path = sprintf("%sskins/%s/", $this->module_path, $iconshop_info->skin);
		}
		$this->setTemplatePath($template_path);

		/**
		 * 아이콘샵 전반적으로 사용되는 javascript 추가
		 **/
		Context::addJsFile($this->module_path . 'tpl/js/iconshop.js');
	}

	/**
	 * @brief 상점페이지 출력
	 **/
	function dispIconshopIndex()
	{
		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');
		$oIconshopModel = getModel('iconshop');

		// 로그인정보 가져오기
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}
		$logged_info = Context::get('logged_info');

		// 포인트/레벨을 구해옴
		$logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
		$point_config = $oModuleModel->getModuleConfig('point');
		$logged_info->point_level = $oPointModel->getLevel($logged_info->point, $point_config->level_step);

		// 상품정보 가져오기
		$icon_list = $oIconshopModel->getShopIconList(10);

		Context::set('menu', 'shop');
		Context::set('total_count', $icon_list->total_count);
		Context::set('total_page', $icon_list->total_page);
		Context::set('page', $icon_list->page);
		Context::set('icon_list', $icon_list->data);
		Context::set('page_navigation', $icon_list->page_navigation);
		$this->setTemplateFile('shop');
	}

	/**
	 * @brief 내 보관함페이지 출력
	 **/
	function dispIconshopMyIcon()
	{
		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');
		$oIconshopModel = getModel('iconshop');

		// 로그인정보 가져오기
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}
		$logged_info = Context::get('logged_info');

		// 포인트/레벨을 구해옴
		$logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
		$point_config = $oModuleModel->getModuleConfig('point');
		$logged_info->point_level = $oPointModel->getLevel($logged_info->point, $point_config->level_step);

		// 상품정보 가져오기
		$icon_list = $oIconshopModel->getMyIconList($logged_info->member_srl, 10);

		Context::set('menu', 'my_icon');
		Context::set('total_count', $icon_list->total_count);
		Context::set('total_page', $icon_list->total_page);
		Context::set('page', $icon_list->page);
		Context::set('icon_list', $icon_list->data);
		Context::set('page_navigation', $icon_list->page_navigation);
		$this->setTemplateFile('my_icon');
	}

	/**
	 * @brief 아이콘 선물페이지 출력
	 **/
	function dispIconshopIconSend()
	{
		$this->setLayoutFile('popup_layout');
		$oPointModel = getModel('point');
		$oModuleModel = getModel('module');
		$oIconshopModel = getModel('iconshop');

		// 로그인정보 가져오기
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'msg_not_permitted');
		}
		$logged_info = Context::get('logged_info');

		// 회원의 포인트/레벨을 구해옴
		$logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
		$point_config = $oModuleModel->getModuleConfig('point');
		$logged_info->point_level = $oPointModel->getLevel($logged_info->point, $point_config->level_step);

		// 상품정보 가져오기
		$icon_srl = Context::get('icon_srl');
		$icon_data = $oIconshopModel->getIconBySrl($icon_srl);
		if(!$icon_data)
		{
			return new Object(-1, 'invalid_icon');
		}

		// 조건: 선물가능여부
		if($icon_data->send_limit != "Y")
		{
			return new Object(-1, 'send_limit_error');
		}

		// 조건:레벨 검사
		if($icon_data->level_limit > $logged_info->point_level)
		{
			return new Object(-1, 'level_limit_error');
		}

		// 조건:그룹 검사
		if($icon_data->group_limit && !$oIconshopModel->group_check($logged_info->group_list, $icon_data->group_limit_list))
		{
			return new Object(-1, 'group_limit_error');
		}

		// 조건:이벤트기간 검사
		if($icon_data->event_limit == "Y")
		{
			$now = date("Ymd", time());
			$event_start = zdate($icon_data->event_start, "Ymd");
			$event_end = zdate($icon_data->event_end, "Ymd");
			if($event_start && $now < $event_start)
			{
				return new Object(-1, 'event_limit_error');
			}
			if($event_end && $now > $event_end)
			{
				return new Object(-1, 'event_limit_error');
			}
		}

		// 조건:갯수 검사
		if(!$icon_data->total_count)
		{
			return new Object(-1, 'count_limit_error');
		}

		// 조건:포인트 검사
		if($icon_data->price > $logged_info->point)
		{
			return new Object(-1, 'point_limit_error');
		}

		Context::set('menu', 'my_icon');
		Context::set('icon_data', $icon_data);
		Context::set('point_name', $point_config->point_name);
		$this->setTemplateFile('my_icon');

		$this->setTemplateFile('icon_send');
	}

	/**
	 * @brief 회원검색
	 **/
	function dispIconshopMemberSearch()
	{
		$oMemberModel = getModel('member');

		$params = Context::gets('search_target', 'search_keyword', 'target_id');
		$result_list = array();
		if($params->search_target && $params->search_keyword)
		{
			switch($params->search_target)
			{
				case "member_srl":
					$params->search_keyword = (int)$params->search_keyword;
					$output = $oMemberModel->getMemberInfoByMemberSrl($params->search_keyword);
					$result_list[] = $output;
					break;
				case "nick_name":
					$params->search_keyword = str_replace(' ', '%', $params->search_keyword);
					$args = new stdClass();
					$args->s_nick_name = $params->search_keyword;
					$args->page = Context::get('page');
					$args->list_count = 10;
					$args->page_count = 1;
					$args->sort_index = "member_srl";
					$args->sort_order = "desc";
					$output = executeQuery("member.getMemberList", $args);
					$result_list = $output->data;
					break;
				default:
					unset($params->search_keyword);
			}
		}
		if($result_list)
		{
			Context::set('result_list', $result_list);
		}

		// 레이아웃을 팝업으로 지정
		$this->setLayoutFile('popup_layout');

		// 예외적으로 템플릿경로 변경
		$this->setTemplatePath($this->module_path . "tpl");

		// 템플릿 파일 지정
		$this->setTemplateFile('member_search');
	}
}
