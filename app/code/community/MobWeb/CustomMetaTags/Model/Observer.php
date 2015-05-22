<?php

class MobWeb_CustomMetaTags_Model_Observer
{
	public function controllerActionLayoutGenerateBlocksAfter($observer)
	{
		// Check the page type we're currently on
		$controllerName = Mage::app()->getRequest()->getControllerName();

		if($controllerName === 'category') {

			// Load the current category
			$category = Mage::registry('current_category');

			// Get the current category's name
			$categoryName = $category->getName();

			// Get a reference to the head block
			$head = $observer->getLayout()->getBlock('head');

			// Update the head's meta title
			$head->setTitle(sprintf('%s - Just an example', $categoryName));
		}

		return $observer;
	}
}