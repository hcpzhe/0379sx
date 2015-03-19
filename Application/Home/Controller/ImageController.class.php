<?php
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Think\Model;

class ImageController extends HomeBaseController {
	
	/**
	 * 图片列表
	 */
	public function lists() {
		$this->display();
	}
	
	/**
	 * 图片内容
	 */
	public function info() {
		$this->display();
	}
	
}