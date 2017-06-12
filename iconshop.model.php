<?php

/**
 * @class  iconshopModel
 * @author 러키군 (admin@barch.kr)
 * @brief  iconshop 모듈의 Model class
 **/
class iconshopModel extends module
{
	/**
	 * @brief 초기화
	 **/
	function init()
	{
	}

	/**
	 * @brief icon_srl로 상품정보 구해옴
	 **/
	function getIconBySrl($icon_srl)
	{
		if(!$icon_srl)
		{
			return;
		}
		$args = new stdClass();
		$args->icon_srl = $icon_srl;
		$output = executeQuery("iconshop.getIcon", $args);

		if($output->data)
		{
			$output->data = $this->varSetting($output->data);
		}
		return $output->data;
	}

	/**
	 * @brief 이름으로 상품정보 구해옴
	 **/
	function getIconByTitle($title)
	{
		$args = new stdClass();
		$args->title = $title;
		$output = executeQuery("iconshop.getIcon", $args);

		if($output->data)
		{
			$output->data = $this->varSetting($output->data);
		}
		return $output->data;
	}

	/**
	 * @brief 상품목록 구해옴 (상점)
	 **/
	function getShopIconList($list_count = 10, $page_count = 10)
	{
		// 모듈설정 가져옴
		$iconshop_config = Context::get('module_config');

		// 검색/정렬옵션
		$search_keyword = trim(Context::get('search_keyword'));
		$args = new stdClass();
		$args->s_title = $search_keyword;
		$args->sort_index = "icon_srl";
		$args->sort_order = "desc";

		// 기타 변수들 정리
		$args->page = Context::get('page');
		$args->list_count = ($iconshop_config->list_count) ? $iconshop_config->list_count : 10;
		$args->page_count = (int)$page_count;

		$output = executeQuery("iconshop.getIconList", $args);

		if($output->data)
		{
			if(is_array($output->data))
			{
				foreach($output->data as $key => $val)
				{
					$output->data[$key] = $this->varSetting($val);
				}
			}
			else
			{
				$output->data = $this->varSetting($output->data);
			}
		}
		return $output;
	}

	/**
	 * @brief data_srl로 회원 상품정보 구해옴
	 **/
	function getMemberIconByDataSrl($data_srl)
	{
		if(!$data_srl)
		{
			return;
		}
		$args = new stdClass();
		$args->data_srl = $data_srl;
		$output = executeQuery("iconshop.getMemberIcon", $args);
		if(!$output->toBool())
		{
			return $output;
		}
		else
		{
			if($output->data->group_limit)
			{
				$output->data->group_limit_list = explode(",", $output->data->group_limit);
			}
			return $output->data;
		}
	}

	/**
	 * @brief icon_srl + member_srl로 회원 상품정보 구해옴
	 **/
	function getMemberIconByIconSrl($icon_srl, $member_srl)
	{
		if(!$icon_srl || !$member_srl)
		{
			return;
		}
		$args = new stdClass();
		$args->icon_srl = $icon_srl;
		$args->member_srl = $member_srl;

		$output = executeQuery("iconshop.getMemberIcon", $args);
		if(!$output->toBool())
		{
			return $output;
		}
		else
		{
			if($output->data->group_limit)
			{
				$output->data->group_limit_list = explode(",", $output->data->group_limit);
			}
			return $output->data;
		}
	}

	/**
	 * @brief 회원의 대표아이콘 구해옴
	 **/
	function getMemberIconBySelected($member_srl)
	{
		if(!$member_srl)
		{
			return;
		}
		$args = new stdClass();
		$args->member_srl = $member_srl;
		$args->is_selected = "Y";
		$output = executeQuery("iconshop.getMemberIconBySelected", $args);
		if($output->data)
		{
			$output->data = $this->varSetting($output->data);
		}
		return $output->data;
	}

	/**
	 * @brief 내 보관함 리스트 출력
	 **/
	function getMyIconList($member_srl, $list_count = 10, $page_count = 10)
	{
		// 모듈설정 가져옴
		$iconshop_config = Context::get('module_config');

		// 검색/정렬옵션
		$search_keyword = trim(Context::get('search_keyword'));
		$args = new stdClass();
		$args->s_member_srl = $member_srl;
		$args->s_title = $search_keyword;
		$args->sort_index = "member.data_srl";
		$args->sort_order = "desc";

		// 기타 변수들 정리
		$args->page = Context::get('page');
		$args->list_count = ($iconshop_config->list_count) ? $iconshop_config->list_count : 10;
		$args->page_count = $page_count;
		$output = executeQuery("iconshop.getMemberIconListWithAdmin", $args);

		if($output->data)
		{
			if(is_array($output->data))
			{
				foreach($output->data as $key => $val)
				{
					$output->data[$key] = $this->varSetting($val);
				}
			}
			else
			{
				$output->data = $this->varSetting($output->data);
			}
		}
		return $output;
	}

	/**
	 * @brief 회원의 상품보유갯수 구해옴
	 **/
	function getMemberIconCount($member_srl)
	{
		$args = new stdClass();
		$args->member_srl = $member_srl;
		$output = executeQuery("iconshop.getMemberIconCount", $args);
		return (int)$output->data->count;
	}

	function group_check($member_group_list, $icon_group_list)
	{
		$i = 0;
		foreach($member_group_list as $key => $val)
		{
			if(in_array($key, $icon_group_list))
			{
				$i = 1;
				break;
			}
		}
		return $i;
	}

	// object의 값을 쓰기 편하게 세팅
	function varSetting($data)
	{
		if(!$data)
		{
			return;
		}
		$data->title = htmlspecialchars($data->title);
		if($data->group_limit)
		{
			$data->group_limit_list = explode(",", $data->group_limit);
		}
		if(isset($data->total_count))
		{
			if($data->total_count == -1)
			{
				$data->total_count2 = Context::getLang('count_infinite');
			}
			elseif($data->total_count > 0)
			{
				$data->total_count2 = sprintf("%d%s", $data->total_count, Context::getLang('unit_count'));
			}
			else
			{
				$data->total_count2 = Context::getLang('sellout_count');
			}
		}

		// 설정 정보 가져오기
		$oModuleModel = &getModel('module');
		$iconshop_config = $oModuleModel->getModuleConfig('iconshop');
		$point_config = $oModuleModel->getModuleConfig('point');
		Context::set('point_name', $point_config->point_name);
		$data->price2 = sprintf("%d%s", $data->price, $point_config->point_name);
		if($data->minute_limit == "Y")
		{
			$data->minute2 = $data->minute . Context::getLang('unit_min');
			$data->end_date2 = zdate($data->end_date, "Y/m/d H:i:s");
		}
		else
		{
			$data->minute2 = Context::getLang('count_infinite');
			$data->end_date2 = Context::getLang('count_infinite');
		}
		$data->icon = "";
		$time_check = date("YmdHis", time() - $iconshop_config->new_hour * 60 * 60);
		if($data->event_limit == "Y")
		{
			$data->icon.= sprintf("<img src='%stpl/icons/event.gif' title='event' alt='event' />&nbsp;",$this->module_path);
		}
		if($data->minute_limit == "Y")
		{
			$data->icon.= sprintf("<img src='%stpl/icons/time.gif' title='time' alt='ime' />&nbsp;",$this->module_path);
		}
		if($data->regdate > $time_check)
		{
			$data->icon.= sprintf("<img src='%stpl/icons/new.gif' title='new' alt='new' />&nbsp;",$this->module_path);
		}
		return $data;
	}

	/**
	 * @brief data_srl로 로그정보 구해옴
	 **/
	function getLogByDataSrl($data_srl)
	{
		if(!$data_srl)
		{
			return;
		}
		$args = new stdClass();
		$args->data_srl = $data_srl;
		$output = executeQuery("iconshop.getLog", $args);
		if(!$output->toBool())
		{
			return $output;
		}
		else
		{
			if($output->data->group_limit)
			{
				$output->data->group_limit_list = explode(",", $output->data->group_limit);
			}
			return $output->data;
		}
	}
}
