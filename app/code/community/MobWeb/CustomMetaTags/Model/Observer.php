<?php

class MobWeb_CustomMetaTags_Model_Observer
{
	public function controllerActionLayoutGenerateBlocksAfter($observer)
	{
		// Check the page type we're currently on
		$controllerName = Mage::app()->getRequest()->getControllerName();

		// Category pages
		if($controllerName === 'category') {

			// Load the current category
			$entity = Mage::registry('current_category');

			// Check the category's "Display Mode". If it set to "Static Block Only", don't overwrite the meta tags
			if($entity->getDisplayMode() === 'PAGE') {
				return $observer;
			}

			// Get the meta tags
			$metaTags = Mage::helper('custommetatags')->createTagsFromTemplateSet(1, $entity);
		}

		// Product pages
		if($controllerName === 'product') {

			// Load the current product
			$entity = Mage::registry('current_product');

			// Get the meta tags
			$metaTags = Mage::helper('custommetatags')->createTagsFromTemplateSet(2, $entity);
		}

		// Check if the meta tags have been created
		if(isset($metaTags)) {

			// If a meta tag has been set explicitely for the current entity, don't overwrite it
			if($entity->getMetaTitle()) {
				unset($metaTags['title']);
			}

			if($entity->getMetaDescription()) {
				unset($metaTags['description']);
			}

			// Update the meta tags
			if($head = $observer->getLayout()->getBlock('head')) {

				// Check if a title has been set
				if(isset($metaTags['title'])) {
					$head->setTitle($metaTags['title']);
				}

				// Check if a description has been set
				if(isset($metaTags['description'])) {
					$head->setDescription($metaTags['description']);
				}

				// Check if a keyword string has been set
				if(isset($metaTags['keywords'])) {

					// For the keywords, ALWAYS append our custom keywords to the existing keywords
					$head->setKeywords($entity->getMetaKeywords() . ', ' . $metaTags['keywords']);
				}
			}
		}

		return $observer;
	}
}