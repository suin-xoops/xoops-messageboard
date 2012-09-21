<?php

class MessageBoard_Service extends XCube_ActionFilter
{
	public function preBlockFilter()
	{
		$file = __DIR__.'/../class/MessageBoard/ApplicationService.php';
		$this->mRoot->mDelegateManager->add('MessageBoard.CreateBoard', '\MessageBoard\ApplicationService::createBoard', $file);
		$this->mRoot->mDelegateManager->add('MessageBoard.AddMember', '\MessageBoard\ApplicationService::addMember', $file);
		$this->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.MessageBoard.ShowBoard', '\MessageBoard\ApplicationService::showBoard', $file);
	}
}
