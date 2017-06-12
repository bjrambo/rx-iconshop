<?php
require_once(_XE_PATH_ . 'modules/iconshop/iconshop.view.php');

class iconshopMobile extends iconshopView
{
	function init()
	{
		$oModuleModel = getModel('module');
		$iconshop_info = $oModuleModel->getModuleInfoByMid("iconshop");
		$iconshop_config = $oModuleModel->getModuleConfig('iconshop');

		$colorset = $oModuleModel->getModuleSkinVars($iconshop_info->module_srl);

		// 설정 변수 지정
		Context::set('iconshop_info', $iconshop_info);
		Context::set('iconshop_config', $iconshop_config);
		Context::set('cols_list_count', $iconshop_config->cols_list_count);
		Context::set('colorset', $colorset['colorset']->value);
		Context::set('iconshop_main_menu', Context::getLang('iconshop_main_menu'));
		$template_path = sprintf("%sm.skins/%s/", $this->module_path, $iconshop_info->mskin);
		if(!is_dir($template_path) || !$iconshop_info->mskin)
		{
			$iconshop_info->mskin = 'default';
			$template_path = sprintf("%sskins/%s/", $this->module_path, $iconshop_info->mskin);
		}
		$this->setTemplatePath($template_path);
	}
}
