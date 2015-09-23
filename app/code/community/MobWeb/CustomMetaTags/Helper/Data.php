<?php

class MobWeb_CustomMetaTags_Helper_Data extends Mage_Core_Helper_Abstract {

	public function formatPrice($price) {
		return Mage::helper('core')->currency($price, true, false);
	}

	public function getTemplates($setId) {
		$templates = array();
		$templates['title'] = Mage::getStoreConfig("custommetatags/template_set_$setId/title_template");
		$templates['description'] = Mage::getStoreConfig("custommetatags/template_set_$setId/description_template");
		$templates['keywords'] = Mage::getStoreConfig("custommetatags/template_set_$setId/keywords_template");

		return $templates;
	}

	public function getVariables($entity) {
		$variables = array();

		// Loop through all the entity attributes and store them as variables,
		// for example the name will be available as "%NAME%"
		$attributes = $entity->getAttributes();
		foreach($attributes AS $attribute) {
			$attributeName = $attribute->getName();
			$attributeValue = $attribute->getFrontend()->getValue($entity);

			if($attributeName && $attributeValue) {
				$variables['%' . strtoupper($attributeName) . '%'] = $attributeValue;
			}
		}

		// Custom variables for categories
		if($entity INSTANCEOF Mage_Catalog_Model_Category) {

			// Parent category name, but only if it's not the root category
			$variables['%PARENT_CATEGORY_NAME%'] = '';
			if($parentCategory = $entity->getParentCategory()) {

				$defaultCategoryNames = array(
					'Default Category',
					'Root Category',
					'Standardkategorie'
				);

				if(!in_array($parentCategory->getName(), $defaultCategoryNames)) {
					$variables['%PARENT_CATEGORY_NAME%'] = $parentCategory->getName();
				}
			}

			// Main category, meaning the category at the first level below the root category
			$variables['%MAIN_CATEGORY_NAME%'] = '';
			$categoryPathIds = explode('/', $entity->getPath());
			if(count($categoryPathIds) > 1) {
				$variables['%MAIN_CATEGORY_NAME%'] = Mage::getModel('catalog/category')->load($categoryPathIds[2])->getName();
			}
		}

		// Custom variables for products
		if($entity INSTANCEOF Mage_Catalog_Model_Product) {

			// Product catalog price (properly formatted)
			$variables['%PRICE%'] = $this->formatPrice($entity->getPrice());

			// Product catalog special price
			$variables['%PRICE_SPECIAL%'] = $this->formatPrice($entity->getSpecialPrice());

			// Product catalog rule price
			$variables['%PRICE_CATALOG_RULES%'] = $this->formatPrice(Mage::getModel('catalogrule/rule')->calcProductPriceRule($entity, $entity->getPrice()));
		}

		// Formatted website domain of the current store, e.g. google.com instead of http://www.google.com/
		$variables['%DOMAIN%'] = str_replace(array('http://', '/'), '', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));

		// Unset the variables where the value is not a string (e.g. an array)
		foreach($variables AS $key => $value) {
			if(!is_string($value)) {
				unset($variables[$key]);
			}
		}

		return $variables;
	}

	public function getProcessedTemplates($templates, $variables) {
		$processedTemplates = array();

		// Loop through the templates
		foreach($templates AS $templateKey => $template) {

			// Replace the variables in the template with their values
			$processedTemplates[$templateKey] = str_replace(array_keys($variables), array_values($variables), $template);
		}

		return $processedTemplates;
	}

	public function createTagsFromTemplateSet($templateSetId, $entity) {

		// Set the cache key for the entity / template combination
		$cacheKey = get_class($entity) . '_' . $entity->getId() . '_' . $templateSetId;

		// If the processed templates are in the cache, load them from there
		$cache = Mage::getSingleton('core/cache');
		if($processedTemplates = $cache->load($cacheKey)) {
			return unserialize($processedTemplates);
		}

		// Load the templates from the configuration
		$templates = Mage::helper('custommetatags')->getTemplates($templateSetId);

		// Get the variables to use in the templates
		$variables = Mage::helper('custommetatags')->getVariables($entity);

		// Replace the variables in the templates templates
		$processedTemplates = Mage::helper('custommetatags')->getProcessedTemplates($templates, $variables);

		// Save the processed templates in the cache
		$cache->save(serialize($processedTemplates), $cacheKey, array(Mage_Catalog_Model_Product::CACHE_TAG), 60*60*24*7);

		return $processedTemplates;
	}
}